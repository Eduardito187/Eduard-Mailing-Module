<?php

namespace Eduard\Mailing\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Eduard\Account\Models\Client;
use Eduard\Search\Models\IndexCatalog;
use Eduard\Mailing\Models\Mailing;
use Eduard\Mailing\Models\MailingCustomer;

class MailingIndex extends Model
{
    use HasFactory;

    protected $table = 'mailing_index';
    protected $fillable = ['send'];
    protected $hidden = ['id_client', 'id_index', 'id_mail', 'created_at', 'updated_at'];
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;

    /**
     * @inheritDoc
     */
    public function allCustomers() {
        return $this->hasMany(MailingCustomer::class, 'id_mailing_index', 'id');
    }

    /**
     * @inheritDoc
     */
    public function client() {
        return $this->hasOne(Client::class, 'id', 'id_client');
    }

    /**
     * @inheritDoc
     */
    public function index() {
        return $this->hasOne(IndexCatalog::class, 'id', 'id_index');
    }

    /**
     * @inheritDoc
     */
    public function mail() {
        return $this->hasOne(Mailing::class, 'id', 'id_mail');
    }
}