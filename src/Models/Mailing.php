<?php

namespace Eduard\Mailing\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Eduard\Mailing\Models\MailingIndex;
use Eduard\Account\Models\Client;

class Mailing extends Model
{
    use HasFactory;

    protected $table = 'mailing';
    protected $fillable = ['name', 'description', 'run_date', 'send', 'template', 'preview_mail'];
    protected $hidden = ['id_client', 'created_at', 'updated_at'];
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;

    /**
     * @inheritDoc
     */
    public function allMailingIndex() {
        return $this->hasMany(MailingIndex::class, 'id_mail', 'id');
    }

    /**
     * @inheritDoc
     */
    public function client() {
        return $this->hasOne(Client::class, 'id', 'id_client');
    }
}