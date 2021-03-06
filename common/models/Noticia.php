<?php

namespace common\models;

use Yii;
use \yii\behaviors\SluggableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use \yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "noticia".
 *
 * @property integer $id
 * @property string $titulo
 * @property string $seo_slug
 * @property string $detalle
 * @property integer $categoria_id
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 *
 * @property Comentarios[] $comentarios
 * @property Categoria $categoria
 * @property User $createdBy
 * @property User $updatedBy
 */
class Noticia extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'noticia';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['titulo', 'detalle', 'categoria_id'], 'required'],
            [['categoria_id', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['titulo', 'seo_slug'], 'string', 'max' => 100],
            [['categoria_id'], 'exist', 'skipOnError' => true, 'targetClass' => Categoria::className(), 'targetAttribute' => ['categoria_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'titulo' => 'Titulo',
            'seo_slug' => 'Seo Slug',
            'detalle' => 'Detalle',
            'categoria_id' => 'Categoria ID',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComentarios()
    {
        return $this->hasMany(Comentarios::className(), ['id_noticia' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategoria()
    {
        return $this->hasOne(Categoria::className(), ['id' => 'categoria_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }
    
    public function behaviors() {
        return [
            
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => new Expression('NOW()'),
            ],
            'blameable' => [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
            [
                'class' => SluggableBehavior::className(),
                'attribute' => 'titulo',
                'slugAttribute' => 'seo_slug',
            ],            
        ];
    }
    public function getAlleft($slug){
        
        $query=new \yii\db\Query();
        $query->select( ['noticia.*','noticia.titulo AS nTitulo','noticia.detalle AS nDetalle','noticia.categoria_id AS nCategoria','noticia.created_by AS nAutor','noticia.created_at AS nFecha'])->from('noticia')->where(['noticia.seo_slug'=>$slug]);
        $cmd=$query->createCommand();
        $noticia=$cmd->queryAll();
        return $noticia;
        
        
        
    }
}
