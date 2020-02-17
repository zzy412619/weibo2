<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
	// Eloquent 将会假设 Article 模型被存储记录在 articles 数据表中
    //指定自己的数据表，则可以通过 table 属性来定义
    protected $table = 'my_articles';
}
