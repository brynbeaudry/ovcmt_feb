<?php

namespace App\Http\Controllers;
use DB;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\News;
use Illuminate\Support\Facades\Input;
use Image;
use Illuminate\Http\UploadedFile;
use Storage;

class NewsController extends Controller
{
	public function getNewsData(Request $req) {
		$news = DB::table('info_news')
			->where('news_id', $req->news_id)
			->where('news_title', $req->news_title)
			->get();
		return response()->json(array("newsdata" => $news, 200));
	}

	//Fetches Data from the database
	public function index()
	{
		// /* Gets the values from a single column */
		// $titles = DB::table('info_news')->pluck('news_title');
		// foreach($titles as $titles)
		// {
			// echo $titles;
		// }
		
		$getalldata = DB::table('info_news')
		
		->orderBy('news_id', 'DESC')
		->get();
		//compact() is a php function that creates an array containing
		//variables and their values
        return view('pages.newsPage',compact('getalldata'));										 
	}

	public function store(Request $req)
	{		
		$user = new News;

		$user->news_title = $req->news_title;
		$user->news_link = $req->news_link;
		$user->news_publish_date = $req->news_publish_date;
		$user->news_author = $req->news_author;
		$user->news_full_content = $req->news_full_content;

		if($req->hasFile('news_image'))
		{
			$fileName = $req->news_image->getClientOriginalName();
			$req->news_image->move(public_path('/images'), $fileName);
			
			$user->news_image = $fileName;
		}
		
			//return redirect()->action('NewsController@index')->with('error', 'Not Created successfully!');
		

		// if(Input::hasFile('news_image'))
		// {
			// $destination = 'ovcmt/public/images/';
			// $extension = Input::file('news_image')->getClientOriginalName();
			// $fileName = Input::get('news_image').".".$extension;
			// Input::file('news_image')->move($destination, $fileName);
			// $user->news_image = $fileName;
		// }
		
		$user->save();
		//dd($req->all());
			return redirect()->action('NewsController@index')->with('message', 'News Created successfully!');
	}
	
	public function showImage()
	{
		$user = News::all();
		return view('pages.newspage', ['news_image' => $user]);
	}
	
	public function editNews(Request $req) {
        $news = DB::table('info_news')
            ->where('news_id', $req->modal_news_id_edit)
            ->where('news_title', $req->modal_news_title_original);

        $title   = $news->update(['news_title' => $req->modal_news_title_edit]);
        $link    = $news->update(['news_link' => $req->modal_news_link_edit]);
        $date    = $news->update(['news_publish_date' => $req->modal_news_publish_date_edit]);
        $author  = $news->update(['news_author' => $req->modal_news_author_edit]);
        $content = $news->update(['news_full_content' => $req->modal_news_full_content_edit]);
	    
		
		//dd($req->all());
	
        if ($title != 0 || $link != 0 || $date != 0 || $author != 0 || $content != 0) {
        	if($req->hasFile('modal_news_image_edit'))
			{
				$fileName = $req->modal_news_image_edit->getClientOriginalName();
				$req->modal_news_image_edit->move(public_path('/images'), $fileName);
				
				// $news->news_image = $fileName; 
				
				$image   = $news->update(['news_image' => $fileName]);
				
				return redirect()->action('NewsController@index')->with('message', 'there is file.');
			}
			else {
				//return response()->json(array("newsdata" => $req->all(), 200));
				return redirect()->action('NewsController@index')->with('message', 'Updated succesfully.');
				// return redirect()->action('NewsController@index')->with('error', $req->all());
			}
			// return redirect()->action('NewsController@index')->with('message', 'Updated succesfully.');
        } else {
			if($req->hasFile('modal_news_image_edit'))
			{
				$fileName = $req->modal_news_image_edit->getClientOriginalName();
				$req->modal_news_image_edit->move(public_path('/images'), $fileName);
				
				// $news->news_image = $fileName; 
				
				$image   = $news->update(['news_image' => $fileName]);
				
				return redirect()->action('NewsController@index')->with('message', 'Updated Successfully.');
			}
			else {
				//return response()->json(array("newsdata" => $req->all(), 200));
				return redirect()->action('NewsController@index')->with('message', 'Updated Successfully.');
				// return redirect()->action('NewsController@index')->with('error', $req->all());
			}
        	// return redirect()->action('NewsController@index')->with('error', 'Couldn\'t update post.');
        }
    }
	
	
	public function deleteNews(Request $req) {
		$news = DB::table('info_news')
            ->where('news_id', $req->modal_news_id_delete)
            ->where('news_title', $req->modal_news_title_delete);

        $deleted = $news->delete();
        if ($deleted != 0) {
        	return redirect()->action('NewsController@index')->with('message', 'Deleted Post Successfully.');
        } else {
        	return redirect()->action('NewsController@index')->with('error', 'Couldn\'t Delete Post.');
        }
	}
}
