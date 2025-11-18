<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $created_by
 * @property string $topic_name
 * @property string $menu_icon
 * @property string $section_class
 * @property string $remark
 * @property string $created_at
 * @property string $updated_at
 * @property UmUser $umUser
 * @property PmInterface[] $pmInterfaces
 */
class InterfaceTopics extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pm_interface_topic';
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = ['created_by', 'topic_name', 'menu_icon', 'section_class', 'remark', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function umUser()
    {
        return $this->belongsTo('App\UmUser', 'created_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pmInterfaces()
    {
        return $this->hasMany('App\PmInterface');
    }
}
