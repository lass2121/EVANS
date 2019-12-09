<?php

namespace app\controllers;

use Yii;
use app\models\ProgramKerja;
use app\models\BentukKegiatan;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProgramKerjaController implements the CRUD actions for ProgramKerja model.
 */
class ProgramKerjaController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all ProgramKerja models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => ProgramKerja::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ProgramKerja model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ProgramKerja model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $row = array();
        $model = new ProgramKerja();
        $model2 = new BentukKegiatan();

        if ($model->load(Yii::$app->request->post())) {
            if(Yii::$app->request->post('submit1')==='save'){
                $model->STATUS_DRAFT = '1';
            }
            else{
                $model->STATUS_DRAFT = '0'; 
            }
            $idTenggatWaktu = $model->getIDTenggatWaktu('Program Kerja');
            
            $model->ID_TENGGAT_WAKTU = $idTenggatWaktu['ID_TENGGAT_WAKTU'];
            $model->save();
            
            $value = $model->getSeqValue()['LPAD(EVANS_PROGRAM_KERJA_SEQ.CURRVAL,5,0)'];
            foreach(Yii::$app->request->post('BentukKegiatan')['ID_BENTUK_KEGIATAN'] as $IDBentuk){
                $model2 = new BentukKegiatan();
                $model2->ID_BENTUK_KEGIATAN = $IDBentuk;
                $model2->ID_PROKER = $value;
                $model2->save();                
            }
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
            'row' => $row
        ]);
    }

    /**
     * Updates an existing ProgramKerja model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $row = array();
        $data = $model->getCurrentKegiatan($id);

        foreach($data as $key => $val){
            $row[] = $key;
        }

        if ($model->load(Yii::$app->request->post())) {
            // $model->save();
            if(Yii::$app->request->post('submit1')==='save'){
                $model->updateProker(Yii::$app->request->post('ProgramKerja'),$id,'save');
            }
            else{
                $model->updateProker(Yii::$app->request->post('ProgramKerja'),$id,'submit');
            }
            
            $model->deleteBentukKegiatan($id);
            foreach(Yii::$app->request->post('BentukKegiatan')['ID_BENTUK_KEGIATAN'] as $IDBentuk){
                $model2 = new BentukKegiatan();
                $model2->ID_BENTUK_KEGIATAN = $IDBentuk;
                $model2->ID_PROKER = $id;
                $model2->save();
            }
            return $this->redirect(['index']);
        }
        // if ($model->load(Yii::$app->request->post()) && $model->save()) {
        //     return $this->redirect(['view', 'id' => $model->ID_PROKER]);
        // }

        return $this->render('update', [
            'model' => $model,
            'row' => $row            
        ]);
    }

    public function actionCalendar(){
        $model = new \yii\base\DynamicModel([
            'bulan', 'tahun'
        ]);

        $model->addRule(['bulan', 'tahun'], 'required');

        $modelProker = new ProgramKerja();

        $events = ProgramKerja::find()->where(['STATUS_DRAFT' => '0'])->all();

        $search_date = null;
        

        if(Yii::$app->request->post()){
            // var_dump(Yii::$app->request->post('DynamicModel'));
            $bulan = Yii::$app->request->post('DynamicModel')['bulan'];
            // var_dump($bulan);
            $tahun = Yii::$app->request->post('DynamicModel')['tahun'];
            // var_dump($tahun);

            if($bulan != '' && $tahun != ''){
                $events = ProgramKerja::find()
                ->where(['STATUS_DRAFT' => '0'])
                ->andWhere("SUBSTR(TO_CHAR(START_DATE,'dd/mm/yyyy'),7,4) = '$tahun'")
                ->andWhere("SUBSTR(TO_CHAR(START_DATE,'dd/mm/yyyy'),4,2) = '$bulan'")
                ->all();
            }
            $search_date = "$bulan-02-$tahun";
        }

        foreach($events as $event1){
            $array_color = ['#AE0101','green','black','blue','gray','#AE0167','brown','#C88E00','#7701AE','#9DA923'];
            $array_index = array_rand($array_color);
            $rand_color = $array_color[$array_index];
            // $rand_color = '#' . substr(md5(mt_rand()), 0, 6);

            $event = new \yii2fullcalendar\models\Event();
            $event->id = $event1->ID_PROKER;
            $event->title = $event1->NAMA_KEGIATAN;
            $start_date = date('d-M-y', strtotime("$event1->START_DATE  + 1 day"));
            $event->start = $start_date ;
            // $event->start = $event1->START_DATE ;
            $event->end = $event1->END_DATE;
            $event->color = $rand_color;
            $events[] = $event;
            // var_dump($event->start);
            // var_dump($start_date);
        }

        return $this->render('calendar', [
            'search_date' => $search_date,            
            'events' => $events,
            'model' => $model,
            'modelProker' => $modelProker,
        ]);

    }

    public function actionModalCalendar($id){
        $model = ProgramKerja::find()->where(['ID_PROKER' => $id])->one();
      
        return $this->renderAjax('_detail_proker', [
            'model' => $model,
        ]);   
    }

    /**
     * Deletes an existing ProgramKerja model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ProgramKerja model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return ProgramKerja the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProgramKerja::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
