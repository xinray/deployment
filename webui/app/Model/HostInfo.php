<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class HostInfo extends Model
{

    protected $table = 'hostinfo';

    public static function getdeployhosts($product_name)
    {
        $host = Self::select('host')->where('productid', $product_name)->where('env', 'production')->get()->toArray();
        $hosts = explode(",",$host[0]['host']);
        return $hosts;
    }

}