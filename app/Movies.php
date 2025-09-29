<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Movies extends Model
{
    protected $table = 'movie_videos';

    protected $fillable = ['video_title','video_image','added_by','license_price','author','file_id'];


	public $timestamps = false;



	public static function getMoviesInfo($id,$field_name)
    {
    	$movie_info = Movies::where('status','1')->where('id',$id)->first();

		if($movie_info)
		{
			return  $movie_info->$field_name;
		}
		else
		{
			return  '';
		}

	}
    public function genre()
    {
        return $this->belongsTo(Genres::class, 'genre_id');
    }



}
