<?php

namespace app\controllers;

use Yii;
use app\models\Proposal;
use app\models\HalamanPengesahanProposal;
use app\models\HalamanPengantarProposal;
use app\models\HalamanJudulProposal;
use app\models\BabI;
use app\models\BabII;
use app\models\BabIII;
use app\models\BabV;
use app\models\HalamanLampiranProposal;
use app\models\TransaksiKategori;
use app\models\SusunanPanitia;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * MonitoringProposalController implements the CRUD actions for Proposal model.
 */
class MonitoringProposalController extends Controller
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
     * Lists all Proposal models.
     * @return mixed
     */
    public function actionIndex()
    {
        //dataprovider himpunan
        $query_himpunan = Proposal::find()
            ->joinWith('masterRinciOrganisasi.masterPengurusOrganisasi.masterDaftarOrganisasi.masterJenisOrganisasi')
            ->where("JENIS_ORGANISASI = 'Himpunan'");
        
        $dataProvider_himpunan = new ActiveDataProvider([
            'query' => $query_himpunan ,
            // 'pagination' => [
            //     'pageSize' => 10,
            // ],
            'sort' => [
                'defaultOrder' => [
                    'ID_PROPOSAL' => SORT_ASC,
                ]
            ]
        ]);

        //dataprovider UKM/Organisasi
        $query_ukm = Proposal::find()
            ->joinWith('masterRinciOrganisasi.masterPengurusOrganisasi.masterDaftarOrganisasi.masterJenisOrganisasi')
            ->where("JENIS_ORGANISASI = 'UKM/Organisasi'");
        
        $dataProvider_ukm = new ActiveDataProvider([
            'query' => $query_ukm ,
            // 'pagination' => [
            //     'pageSize' => 10,
            // ],
            'sort' => [
                'defaultOrder' => [
                    'ID_PROPOSAL' => SORT_ASC,
                ]
            ]
        ]);

        //dataprovider KPU
        $query_kpu = Proposal::find()
            ->joinWith('masterRinciOrganisasi.masterPengurusOrganisasi.masterDaftarOrganisasi.masterJenisOrganisasi')
            ->where("JENIS_ORGANISASI = 'KPU'");
        
        $dataProvider_kpu = new ActiveDataProvider([
            'query' => $query_kpu ,
            // 'pagination' => [
            //     'pageSize' => 10,
            // ],
            'sort' => [
                'defaultOrder' => [
                    'ID_PROPOSAL' => SORT_ASC,
                ]
            ]
        ]);

        return $this->render('index', [
            'dataProvider_ukm' => $dataProvider_ukm,
            'dataProvider_himpunan' => $dataProvider_himpunan,
            'dataProvider_kpu' => $dataProvider_kpu,
        ]);
    }

    /**
     * Displays a single Proposal model.
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
     * Creates a new Proposal model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Proposal();

        // if ($model->load(Yii::$app->request->post()) && $model->save()) {
        //     return $this->redirect(['view', 'id' => $model->ID_PROPOSAL]);
        // }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if(Yii::$app->request->post('submit1')==='save'){
                $model->STATUS_DRAFT = '1';
            }
            else{
                $model->STATUS_DRAFT = '0'; 
            }
            
            $model->save();

            $id_proposal = $model->getCurrIdProposal();

            return $this->redirect(['update', 'id' => $id_proposal]);
            // return $this->refresh();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionJudul($id){
        $model = new HalamanJudulProposal();
        
        $sql = "SELECT NAMA_FILE_JUDUL FROM EVANS_HAL_JUDUL_PROPOSAL_TBL
                WHERE ID_PROPOSAL = '$id'";
        
        $result = Yii::$app->db->createCommand($sql)->queryOne();
        
        // ada file nya
        if($result != false){
            $nama_file_judul = $result['NAMA_FILE_JUDUL'];
        }
        else{//gak ada file nya
            $nama_file_judul = '';
        }

        if($model->load(Yii::$app->request->post())  ){
            $model->NAMA_FILE_JUDUL = UploadedFile::getInstance($model, 'NAMA_FILE_JUDUL');

            if($model->NAMA_FILE_JUDUL != null){
                $FILE_URL = 'Proposal_' . $model->ID_PROPOSAL  . '_Halaman_Judul.' . $model->NAMA_FILE_JUDUL->extension;

                $model->NAMA_FILE_JUDUL->saveAs('uploads/proposal/' . $FILE_URL );

                $model->NAMA_FILE_JUDUL = $FILE_URL;
                //jika file nya belom ada, maka insert ke database. jika sudah hanya ganti file nya di folder proposal
                if($nama_file_judul == ''){
                    if ($model->validate()) {
                  
                        $model->save();
                        
                        Yii::$app->session->setFlash('success','File berhasil disimpan');
                    }
                    else{
                        Yii::$app->session->setFlash('error','File gagal disimpan');
                    }
                }else{
                    Yii::$app->session->setFlash('success','File berhasil disimpan');
                }
                
            }
            else{
                Yii::$app->session->setFlash('error','File gagal disimpan');
            }
            return $this->refresh();
        }
        
        return $this->render('halamanjudul', [
            'model' => $model,
            'id' => $id,
            'nama_file_judul' => $nama_file_judul,
        ]);
    }

    public function actionPengesahan($id){
        $model = new HalamanPengesahanProposal();

        $sql = "SELECT * FROM EVANS_HAL_PENGESAHAN_PRPSL_TBL WHERE ID_PROPOSAL = '$id'";
        $result = Yii::$app->db->createCommand($sql)->queryOne();
        
        if($result != false){
            $id_pengesahan = $result['ID_HAL_PENGESAHAN'];
            $model = HalamanPengesahanProposal::findOne($id_pengesahan);
        }
        else{
            $id_pengesahan = '';
        }
        if($model->load(Yii::$app->request->post()) && $model->validate() ){
            $model->save();

            Yii::$app->session->setFlash('success','Data berhasil disimpan');
            
            return $this->refresh();
        }

        $sql = "SELECT ID_PROKER FROM EVANS_PROPOSAL_TBL WHERE ID_PROPOSAL = '$id'";
        $result = Yii::$app->db->createCommand($sql)->queryOne();
        $id_proker = $result['ID_PROKER'];
        $detail_proker = $model->getDetailProker($id_proker);
        $nama_org = $detail_proker['NAMA_ORGANISASI'];
        $nama_kegiatan = $detail_proker['NAMA_KEGIATAN'];
        $start_date = $detail_proker['START_DATE'];
        $end_date = $detail_proker['END_DATE'];

        return $this->render('pengesahan', [
            'model' => $model,
            'id' => $id,
            'id_pengesahan' => $id_pengesahan,
            'nama_org' => $nama_org,
            'nama_kegiatan' => $nama_kegiatan,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ]);
    }

    public function actionPengantar($id){
        $model = new HalamanPengantarProposal();

        $sql = "SELECT FILE_HAL_PENGANTAR FROM EVANS_HAL_PENGANTAR_PRPRSL_TBL
                WHERE ID_PROPOSAL = '$id'";
        
        $result = Yii::$app->db->createCommand($sql)->queryOne();
        
        // ada file nya
        if($result != false){
            $file_hal_pengantar = $result['FILE_HAL_PENGANTAR'];
        }
        else{//gak ada file nya
            $file_hal_pengantar = '';
        }

        if($model->load(Yii::$app->request->post())  ){
            $model->FILE_HAL_PENGANTAR = UploadedFile::getInstance($model, 'FILE_HAL_PENGANTAR');

            if($model->FILE_HAL_PENGANTAR != null){
                $FILE_URL = 'Proposal_' . $model->ID_PROPOSAL  . '_Halaman_Pengantar.' . $model->FILE_HAL_PENGANTAR->extension;

                $model->FILE_HAL_PENGANTAR->saveAs('uploads/proposal/' . $FILE_URL );

                $model->FILE_HAL_PENGANTAR = $FILE_URL;
                //jika file nya belom ada, maka insert ke database. jika sudah hanya ganti file nya di folder proposal
                if($file_hal_pengantar == ''){
                    if ($model->validate()) {
                  
                        $model->save();
                        
                        Yii::$app->session->setFlash('success','File berhasil disimpan');
                    }
                    else{
                        Yii::$app->session->setFlash('error','File gagal disimpan');
                    }
                }else{
                    Yii::$app->session->setFlash('success','File berhasil disimpan');
                }
                
            }
            else{
                Yii::$app->session->setFlash('error','File gagal disimpan');
            }
            return $this->refresh();
        }

        return $this->render('katapengantar', [
            'model' => $model,
            'id' => $id,
            'file_hal_pengantar' => $file_hal_pengantar,
        ]);
    }

    public function actionBab1($id){
        $model = new BabI();
        
        $sql = "SELECT FILE_BAB_1 FROM EVANS_HAL_PENDHLUAN_PRPSL_TBL
                WHERE ID_PROPOSAL = '$id'";
        
        $result = Yii::$app->db->createCommand($sql)->queryOne();
        
        // ada file nya
        if($result != false){
            $file_bab_1 = $result['FILE_BAB_1'];
        }
        else{//gak ada file nya
            $file_bab_1 = '';
        }

        if($model->load(Yii::$app->request->post())  ){
            $model->FILE_BAB_1 = UploadedFile::getInstance($model, 'FILE_BAB_1');

            if($model->FILE_BAB_1 != null){
                $FILE_URL = 'Proposal_' . $model->ID_PROPOSAL  . '_Bab_1.' . $model->FILE_BAB_1->extension;

                $model->FILE_BAB_1->saveAs('uploads/proposal/' . $FILE_URL );

                $model->FILE_BAB_1 = $FILE_URL;
                //jika file nya belom ada, maka insert ke database. jika sudah hanya ganti file nya di folder proposal
                if($file_bab_1 == ''){
                    if ($model->validate()) {
                  
                        $model->save();
                        
                        Yii::$app->session->setFlash('success','File berhasil disimpan');
                    }
                    else{
                        Yii::$app->session->setFlash('error','File gagal disimpan');
                    }
                }else{
                    Yii::$app->session->setFlash('success','File berhasil disimpan');
                }
                
            }
            else{
                Yii::$app->session->setFlash('error','File gagal disimpan');
            }
            return $this->refresh();
            
        }

        return $this->render('bab1', [
            'model' => $model,
            'id' => $id,
            'file_bab_1' => $file_bab_1,
        ]);
    }

    public function actionBab2($id){
        $model = new BabII();

        $sql = "SELECT FILE_BAB_2,ID_BAB_2 FROM EVANS_HAL_DESKRIPSI_PRPSL_TBL
                WHERE ID_PROPOSAL = '$id'";
        
        $result = Yii::$app->db->createCommand($sql)->queryOne();
        
        // ada file nya
        if($result != false){
            $file_bab_2 = $result['FILE_BAB_2'];
            $id_bab_2 = $result['ID_BAB_2'];
        }
        else{//gak ada file nya
            $file_bab_2 = '';
            $id_bab_2 = '';
        }

        if($model->load(Yii::$app->request->post())  ){
            $model->FILE_BAB_2 = UploadedFile::getInstance($model, 'FILE_BAB_2');

            if($model->FILE_BAB_2 != null){
                $FILE_URL = 'Proposal_' . $model->ID_PROPOSAL  . '_Bab_2.' . $model->FILE_BAB_2->extension;

                $model->FILE_BAB_2->saveAs('uploads/proposal/' . $FILE_URL );

                $model->FILE_BAB_2 = $FILE_URL;
                //jika file nya belom ada, maka insert ke database. jika sudah hanya ganti file nya di folder proposal
                if($file_bab_2 == ''){
                    if ($model->validate()) {
                  
                        $model->save();
                        
                        Yii::$app->session->setFlash('success','File berhasil disimpan');
                    }
                    else{
                        Yii::$app->session->setFlash('error','File gagal disimpan');
                    }
                }else{
                    Yii::$app->session->setFlash('success','File berhasil disimpan');
                }
                
            }
            else{
                Yii::$app->session->setFlash('error','File gagal disimpan');
            }
            return $this->refresh();
        }
        return $this->render('bab2', [
            'model' => $model,
            'id' => $id,
            'file_bab_2' => $file_bab_2,
            'id_bab_2' => $id_bab_2,
        ]);
    }

    public function actionSusunanKepanitian($id,$id_proposal){
        $model = new SusunanPanitia();

        if($model->load(Yii::$app->request->post()) && $model->validate() ){
            $model->save();

            Yii::$app->session->setFlash('success','Panitia berhasil disimpan');

            return $this->refresh();
        }

        $query = SusunanPanitia::find()->where(['ID_BAB_2' => $id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'defaultOrder' => [
                    'ID_SUSUNAN' => SORT_ASC,
                ]
            ]
        ]);

        return $this->render('susunanpanitia', [
            'dataProvider' => $dataProvider,
            'model' => $model,
            'id' => $id,
            'id_proposal' => $id_proposal,
        ]);
    }

    public function actionBab3($id){
        $model = new BabIII();

        $sql = "SELECT FILE_BAB_3 FROM EVANS_HAL_RENCANA_PRPSL_TBL
                WHERE ID_PROPOSAL = '$id'";
        
        $result = Yii::$app->db->createCommand($sql)->queryOne();
        
        // ada file nya
        if($result != false){
            $file_bab_3 = $result['FILE_BAB_3'];
        }
        else{//gak ada file nya
            $file_bab_3 = '';
        }

        if($model->load(Yii::$app->request->post())  ){
            $model->FILE_BAB_3 = UploadedFile::getInstance($model, 'FILE_BAB_3');

            if($model->FILE_BAB_3 != null){
                $FILE_URL = 'Proposal_' . $model->ID_PROPOSAL  . '_Bab_3.' . $model->FILE_BAB_3->extension;

                $model->FILE_BAB_3->saveAs('uploads/proposal/' . $FILE_URL );

                $model->FILE_BAB_3 = $FILE_URL;
                //jika file nya belom ada, maka insert ke database. jika sudah hanya ganti file nya di folder proposal
                if($file_bab_3 == ''){
                    if ($model->validate()) {
                  
                        $model->save();
                        
                        Yii::$app->session->setFlash('success','File berhasil disimpan');
                    }
                    else{
                        Yii::$app->session->setFlash('error','File gagal disimpan');
                    }
                }else{
                    Yii::$app->session->setFlash('success','File berhasil disimpan');
                }
                
            }
            else{
                Yii::$app->session->setFlash('error','File gagal disimpan');
            }
            return $this->refresh();
        }

        return $this->render('bab3', [
            'model' => $model,
            'id' => $id,
            'file_bab_3' => $file_bab_3,
        ]);
    }

    public function actionBab4($id){
        $model_kategori = new TransaksiKategori();

        $placeholder = new \yii\base\DynamicModel([
            'JENIS_ANGGARAN', 'KATEGORI'
        ]);
        $placeholder->addRule(['JENIS_ANGGARAN', 'KATEGORI'], 'required');

        if($model_kategori->load(Yii::$app->request->post()) && $model_kategori->validate() ){
            $model_kategori->save();

            Yii::$app->session->setFlash('success','Anggaran berhasil disimpan');

            return $this->refresh();
        }

        $query = TransaksiKategori::find()->where(['ID_PROPOSAL' => $id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'defaultOrder' => [
                    'ID_PROPOSAL' => SORT_ASC,
                ]
            ]
        ]);

        return $this->render('bab4', [
            'dataProvider' => $dataProvider,
            'model_kategori' => $model_kategori,
            'placeholder' => $placeholder,
            'id' => $id,
        ]);
    }

    public function actionBab5($id){
        $model = new BabV();

        $sql = "SELECT FILE_BAB_5 FROM EVANS_HAL_PENUTUP_PRPSL_TBL
                WHERE ID_PROPOSAL = '$id'";
        
        $result = Yii::$app->db->createCommand($sql)->queryOne();
        
        // ada file nya
        if($result != false){
            $file_bab_5 = $result['FILE_BAB_5'];
        }
        else{//gak ada file nya
            $file_bab_5 = '';
        }

        if($model->load(Yii::$app->request->post())  ){
            $model->FILE_BAB_5 = UploadedFile::getInstance($model, 'FILE_BAB_5');

            if($model->FILE_BAB_5 != null){
                $FILE_URL = 'Proposal_' . $model->ID_PROPOSAL  . '_Bab_5.' . $model->FILE_BAB_5->extension;

                $model->FILE_BAB_5->saveAs('uploads/proposal/' . $FILE_URL );

                $model->FILE_BAB_5 = $FILE_URL;
                //jika file nya belom ada, maka insert ke database. jika sudah hanya ganti file nya di folder proposal
                if($file_bab_5 == ''){
                    if ($model->validate()) {
                  
                        $model->save();
                        
                        Yii::$app->session->setFlash('success','File berhasil disimpan');
                    }
                    else{
                        Yii::$app->session->setFlash('error','File gagal disimpan');
                    }
                }else{
                    Yii::$app->session->setFlash('success','File berhasil disimpan');
                }
                
            }
            else{
                Yii::$app->session->setFlash('error','File gagal disimpan');
            }
            return $this->refresh();
        }

        return $this->render('bab5', [
            'model' => $model,
            'id' => $id,
            'file_bab_5' => $file_bab_5,
        ]);
    }

    public function actionLampiran($id){
        $model = new HalamanLampiranProposal();

        $sql = "SELECT FILE_LAMPIRAN FROM EVANS_HAL_LAMPIRAN_PRPSL_TBL
                WHERE ID_PROPOSAL = '$id'";
        
        $result = Yii::$app->db->createCommand($sql)->queryOne();
        
        // ada file nya
        if($result != false){
            $file_lampiran = $result['FILE_LAMPIRAN'];
        }
        else{//gak ada file nya
            $file_lampiran = '';
        }

        if($model->load(Yii::$app->request->post())  ){
            $model->FILE_LAMPIRAN = UploadedFile::getInstance($model, 'FILE_LAMPIRAN');

            if($model->FILE_LAMPIRAN != null){
                $FILE_URL = 'Proposal_' . $model->ID_PROPOSAL  . '_Halaman_Lampiran.' . $model->FILE_LAMPIRAN->extension;

                $model->FILE_LAMPIRAN->saveAs('uploads/proposal/' . $FILE_URL );

                $model->FILE_LAMPIRAN = $FILE_URL;
                //jika file nya belom ada, maka insert ke database. jika sudah hanya ganti file nya di folder proposal
                if($file_lampiran == ''){
                    if ($model->validate()) {
                  
                        $model->save();
                        
                        Yii::$app->session->setFlash('success','File berhasil disimpan');
                    }
                    else{
                        Yii::$app->session->setFlash('error','File gagal disimpan');
                    }
                }else{
                    Yii::$app->session->setFlash('success','File berhasil disimpan');
                }
                
            }
            else{
                Yii::$app->session->setFlash('error','File gagal disimpan');
            }
            return $this->refresh();
        }

        return $this->render('lampiran', [
            'model' => $model,
            'id' => $id,
            'file_lampiran' => $file_lampiran,
        ]);
    }

    /**
     * Updates an existing Proposal model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if(Yii::$app->request->post('submit1')==='save'){
                $model->STATUS_DRAFT = '1';

                $model->save();
                Yii::$app->session->setFlash('success','Simpan data berhasil');
            }
            else{
                $sql_judul = "SELECT * FROM EVANS_HAL_JUDUL_PROPOSAL_TBL WHERE ID_PROPOSAL = '$id'";

                $result_judul = Yii::$app->db->createCommand($sql_judul)->queryAll();

                if($result_judul == null){
                    Yii::$app->session->setFlash('error','Tidak ada file Halaman Judul');

                    return $this->refresh();
                }

                $sql_pengesahan = "SELECT * FROM EVANS_HAL_PENGESAHAN_PRPSL_TBL WHERE ID_PROPOSAL = '$id'";

                $result_pengensahan = Yii::$app->db->createCommand($sql_pengesahan)->queryAll();

                if($result_pengensahan == null){
                    Yii::$app->session->setFlash('error','Mohon pastikan data di halaman pengesahan dan tekan tombol Save');

                    return $this->refresh();
                }

                $sql_pengantar = "SELECT * FROM EVANS_HAL_PENGANTAR_PRPRSL_TBL WHERE ID_PROPOSAL = '$id'";

                $result_pengantar = Yii::$app->db->createCommand($sql_pengantar)->queryAll();

                if($result_pengantar == null){
                    Yii::$app->session->setFlash('error','Tidak ada file Kata Pengantar');

                    return $this->refresh();
                }

                $sql_bab1 = "SELECT * FROM EVANS_HAL_PENDHLUAN_PRPSL_TBL WHERE ID_PROPOSAL = '$id'";

                $result_bab1 = Yii::$app->db->createCommand($sql_bab1)->queryAll();

                if($result_bab1 == null){
                    Yii::$app->session->setFlash('error','Tidak ada file Bab 1');

                    return $this->refresh();
                }

                $sql_bab2 = "SELECT * FROM EVANS_HAL_DESKRIPSI_PRPSL_TBL WHERE ID_PROPOSAL = '$id'";

                $result_bab2 = Yii::$app->db->createCommand($sql_bab2)->queryAll();

                if($result_bab2 == null){
                    Yii::$app->session->setFlash('error','Tidak ada file Bab 2');

                    return $this->refresh();
                }
                else{
                    $result_bab2 = Yii::$app->db->createCommand($sql_bab2)->queryOne();

                    $id_bab_2 = $result_bab2['ID_BAB_2'];

                    $sql_susunan = "SELECT * FROM EVANS_SUSUNAN_PANITIA_TBL WHERE ID_BAB_2 = '$id_bab_2'";
                    
                    $result_susunan = Yii::$app->db->createCommand($sql_susunan)->queryAll();

                    if($result_susunan == null){
                        Yii::$app->session->setFlash('error','Mohon Input Susunan Kepanitian pada halaman Bab 2');

                        return $this->refresh();
                    }

                }

                $sql_bab3 = "SELECT * FROM EVANS_HAL_RENCANA_PRPSL_TBL WHERE ID_PROPOSAL = '$id'";

                $result_bab3 = Yii::$app->db->createCommand($sql_bab3)->queryAll();

                if($result_bab3 == null){
                    Yii::$app->session->setFlash('error','Tidak ada file Bab 3');

                    return $this->refresh();
                }

                $sql_bab4 = "SELECT * FROM EVANS_TRANS_KATEGORI_TBL WHERE ID_PROPOSAL = '$id'";

                $result_bab4 = Yii::$app->db->createCommand($sql_bab4)->queryAll();

                if($result_bab4 == null){
                    Yii::$app->session->setFlash('error','Tidak ada data di Bab 4');

                    return $this->refresh();
                }

                $sql_bab5 = "SELECT * FROM EVANS_HAL_PENUTUP_PRPSL_TBL WHERE ID_PROPOSAL = '$id'";

                $result_bab5 = Yii::$app->db->createCommand($sql_bab5)->queryAll();

                if($result_bab5 == null){
                    Yii::$app->session->setFlash('error','Tidak ada file Bab 5');

                    return $this->refresh();
                }

                $sql_lampiran = "SELECT * FROM EVANS_HAL_LAMPIRAN_PRPSL_TBL WHERE ID_PROPOSAL = '$id'";

                $result_lampiran = Yii::$app->db->createCommand($sql_lampiran)->queryAll();

                if($result_lampiran == null){
                    Yii::$app->session->setFlash('error','Tidak ada file Lampiran');

                    return $this->refresh();
                }

                $model->STATUS_DRAFT = '0'; 

                $model->save();

                $session = Yii::$app->session;

                $jns_org = $session->get('jns_org');

                $sql = "SELECT da.ID_DETAIL
                        FROM EVANS_MASTER_ALUR_TBL ma
                        JOIN EVANS_JENIS_ALUR_TBL ja ON (ja.ID_ALUR = ma.ID_ALUR)
                        JOIN EVANS_DETAIL_ALUR_TBL da on (da.ID_JENIS_ALUR = ja.ID_JENIS_ALUR)
                        WHERE ma.NAMA_ALUR = '$jns_org'
                        AND ja.JENIS_DOKUMEN = 'Proposal'
                        AND ja.STATUS = '1'
                        AND da.TINGKAT = '0'
                        AND da.DESKRIPSI LIKE '%Waiting%'
                        ";
                
                $result = Yii::$app->db->createCommand($sql)->queryOne();
        
                $id_detail =  $result['ID_DETAIL'];

                $sql_insert = "INSERT INTO EVANS_TRANS_ALUR_PRPSL_TBL
                                VALUES ('99999',:1,:2,sysdate)";

                Yii::$app->db->createCommand($sql_insert,[
                    ':1' => $id, 
                    ':2' => $id_detail,
                ])->execute();

                Yii::$app->session->setFlash('success','Suubmit proposal berhasil');

                return $this->redirect(['index']);
            }
            
            return $this->refresh();
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDownload($filename){
        $path = Yii::getAlias('@webroot').'/uploads/proposal/'.$filename;
        if (file_exists($path)) {
            return Yii::$app->response->sendFile($path, $filename);
        }
        else{
            Yii::$app->session->setFlash('error','File tidak ditemukan');
            return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
        }
    }

    public function actionDeleteAnggaran($id){
        $sql = "DELETE FROM EVANS_TRANS_KATEGORI_TBL
                WHERE ID_TRANS_KATEGORI = :1
                ";

        Yii::$app->db->createCommand($sql,[':1' => $id])->execute();

        Yii::$app->session->setFlash('success','Anggaran berhasil dihapus');

        return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
    }

    public function actionDeleteSusunan($id){
        $sql = "DELETE FROM EVANS_SUSUNAN_PANITIA_TBL
                WHERE ID_SUSUNAN = :1
                ";

        Yii::$app->db->createCommand($sql,[':1' => $id])->execute();

        Yii::$app->session->setFlash('success','Panitia berhasil dihapus');

        return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
    }

    public function actionFindKategori(){
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $id_jenis = $parents[0];
                $sql = "SELECT DISTINCT KATEGORI as ".'"id"'.", KATEGORI as ".'"name"'."
                        FROM EVANS_MSTR_KATEGORI_TBL
                        WHERE ID_JENIS = '$id_jenis' 
                        ";
                $out = Yii::$app->db->createCommand($sql)->queryAll();

                return ['output'=> $out, 'selected'=>''];
            }
        }
        return ['output'=>'', 'selected'=>''];
    }

    public function actionFindSumber(){
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $ids = $_POST['depdrop_parents'];
            $jenis = empty($ids[0]) ? null : $ids[0];
            $kategori = empty($ids[1]) ? null : $ids[1];
            if ($kategori != null) {
                $sql = "SELECT ID_KATEGORI as ".'"id"'.", DESKRIPSI as ".'"name"'."
                        FROM EVANS_MSTR_KATEGORI_TBL
                        WHERE ID_JENIS = '$jenis'  AND KATEGORI = '$kategori'
                        ";
                $out = Yii::$app->db->createCommand($sql)->queryAll();

                return ['output'=> $out, 'selected'=>'']; 
            }
        }
        return ['output'=>'', 'selected'=>''];
    }

    /**
     * Deletes an existing Proposal model.
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
     * Finds the Proposal model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Proposal the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Proposal::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
