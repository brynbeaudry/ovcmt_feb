<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
	
	protected $table = 'info_news';
	protected $primaryKey = 'news_id';	
	public $timestamps = false;
    protected $fillable = ['news_title',
						   'news_blob',
						   'news_publish_date',
						   'news_author',
						   'news_link',
						   'news_image',
						   'news_full_content',
	];
}