<?php
	/*
		文件缓存类别，需要预定义所有的变量以及其初始值，不存在就抛出异常;需要Yii::app()->cache
			Yii::app()->cache 方法
				delete(id)
				set(id,value)
				get(id) 返回 === false 说明此变量不可用
			数据库中有T_cache[id,name,val],name是唯一的索引;对应model MyCache
			
		//f::set("siteName","中国护理网在线招聘网站");
		//echo Text::json_encode_ch("中国");
		echo f::get("siteName");
	*/
	class f
	{
		//获取变量，当cache中没有此值的时候，从数据库获取，获取了就补充到缓存中,最后才使用初始值
		public static function get($word)
		{
			//首先检查word是否在预定义中
			if(!isset(self::$words2var[$word]))
			{
				throw new exception("no such cache");
				return false;
			}
			$val = Yii::app()->cache->get($word);
			//cache中不存在（或者这个值就是false,不管）
			if($val === false)
			{
				//到数据库中获取
				$MyCache = MyCache::model()->find("name=:n",array(":n"=>$word));
				//数据库也不存在,取默认值
				if($MyCache == NULL)
				{
					$val = self::$words2var[$word];
				}
				else
				{
					//数据库中存在，那么设置到cache中
					//echo $word;
					//echo $MyCache->val;
					$val = json_decode($MyCache->val,true);
					Yii::app()->cache->set($word,$val);
				}
			}
			return $val;
		}
		//设置变量
		public static function set($word,$val)
		{
			//首先检查word是否在预定义中
			if(!isset(self::$words2var[$word]))
			{
				throw new exception("no such cache");
				return false;
			}
			//设置到缓存里
			Yii::app()->cache->set($word,$val);
			//设置到数据库里
			$MyCache = MyCache::model()->find("name=:n",array(":n"=>$word));
			if($MyCache == NULL)
			{
				$MyCache = new MyCache();
				$MyCache->name = $word;
			}
			$MyCache->val = Text::json_encode_ch($val);
			if(!$MyCache->save())
			{
				throw new Exception("cache not saved in database.");
				return false;
			}
			return true;
		}
		public static $words2var = array(
			"siteName" => "Daisy",
			"smtpType" => 1,//1->smtp服务器发送
			"smtpAddress" => "smtp.example.com",
			"smtpAccount" => "example@example.com",
			"smtpPw" => "example",
			"smtpName" => "Myself",//邮件发送者显示的名字
			"smtpPort" => 25,
			"videoWidth" => 320,
			"videoPath" => "assets/videos/",
			"videoImgPath" => "assets/video_imgs/",
			"tempPath" => "assets/temp/", // temp path to storing the video list for training
			"gunshotGraphPath" => "assets/gunshotGraph/",// for storing the gunshot detection graph, with runId.png
			"personGraphPath" => "assets/personGraph/",// for storing the person detection graph, with runId.png
			"countGunshotPath" => "assets/countGunshotGraph/",
			"videoStuffPath" => "assets/stuff/", //=> /dataset/videoname.mp4/thumbnail.png, .../signAudio.mp3
			"videopreimagename" => "thumbnail.png",
			"videopresignaudioname" => "signAudio.mp3",
			//die(trim(split("index.php",$_SERVER['REQUEST_URI'])[0],'/')); // got daisy.
			// no use, this is a static var. need somehing else.
			//"pythonCallback" => "127.0.0.1/".trim(split("index.php",$_SERVER['REQUEST_URI'])[0],'/')."/index.php/main/done", // still need "127.0.0.1".Yii::app()->baseUrl.f::get("pythonCallback");

			"pythonCallbackMain" => "127.0.0.1", // add Yii::app()->baseUrl
			"pythonCallbackCtr" => "/index.php/python/", // add the action 
			"ppythonMD5key" => "daisyjunweil",
			//constant for comunicating with python are in PP.php

			//for localization
			"locPath" => "/home/junweil/locPath/",
			//"locPath" => "/home/chunwaileong/locPath/",
			"locResultPath" => "/home/dfan/human-rights/ResearchWebsite/media/database/mappings/",
			//"locResultPath" => "/home/chunwaileong/htdocs/daisy/testLoc/mappings/",

			"rashJsonPath" => "assets/rashJsonPath/",//path to save a json file to run video player

			// for sync refinement
			"erRefinePath" => "assets/erRefine/",// will mkdir collectionName everytime

			// for video tiler, the output video
			"videoTilerPath" => "assets/videoTiler/",//tiled video will be here
		);
	}
?>