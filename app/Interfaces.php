<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $pm_interface_topic_id
 * @property int $created_by
 * @property string $interface_name
 * @property string $path
 * @property string $icon_class
 * @property string $tile_class
 * @property string $remark
 * @property string $created_at
 * @property string $updated_at
 * @property PmInterfaceTopic $pmInterfaceTopic
 * @property UmUser $umUser
 * @property PmInterfaceComponent[] $pmInterfaceComponents
 */
class Interfaces extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pm_interfaces';
    public $timestamps = false;


    /**
     * @var array
     */
    protected $fillable = ['pm_interface_topic_id', 'created_by', 'interface_name', 'path', 'icon_class', 'tile_class', 'remark', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pmInterfaceTopic()
    {
        return $this->belongsTo(InterfaceTopics::class, 'pm_interface_topic_id');
    }

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
    public function pmInterfaceComponents()
    {
        return $this->hasMany('App\PmInterfaceComponent', 'pm_interfaces_id');
    }
}
