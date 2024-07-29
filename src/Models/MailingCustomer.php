<?php

namespace Eduard\Mailing\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Eduard\Mailing\Models\MailingIndex;
use Eduard\Analitycs\Models\WebSiteCustomer;

class MailingCustomer extends Model
{
    use HasFactory;

    protected $table = 'mailing_customer';
    protected $fillable = ['sending'];
    protected $hidden = ['id_mailing_index', 'id_website_customer', 'created_at', 'updated_at'];
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;

    /**
     * @inheritDoc
     */
    public function mailingIndex() {
        return $this->hasOne(MailingIndex::class, 'id', 'id_mailing_index');
    }

    /**
     * @inheritDoc
     */
    public function customerWebSite() {
        return $this->hasOne(WebSiteCustomer::class, 'id', 'id_website_customer');
    }
}