<?php
	use Spore\ReST\BaseService;
	use dflydev\markdown\MarkdownParser;

	require_once "../util/StatusData.php";

	/**
	 *
	 */
	class HTTPStatusService extends BaseService
	{
		/**
		 * @url                 /
		 * @verbs               GET
		 * @template            home.twig
		 * @render              nonAJAX
		 */
		public function home()
		{
			$this->getApp()->redirect("/status");
		}

		/**
		 * @url                 /status
		 * @verbs               GET
		 * @template            list.twig
		 * @render              nonAJAX
		 */
		public function getStatuses($code=null)
		{
			$sd = new StatusData();
			$results = R::findAll("status", !empty($code) ? "WHERE code = ".$code : null);
			if(empty($results) || count($results) <= 0)
				return null;

			$markdown = new MarkdownParser();
			$statuses = array();
			foreach($results as $record)
			{
				$data = $record->export();
				if(empty($data))
					continue;

				$relatedClass = R::$f->begin()->select("*")->from("`class`")
							->where("SUBSTR(codeRange, 1, 1) = '".substr($data["code"], 0, 1)."'")
							->get();

				if(!empty($relatedClass) && count($relatedClass) > 0)
				{
					$relatedClass = $relatedClass[0];
					$relatedClass["description"] = $markdown->transformMarkdown($relatedClass["description"]);
					$data["class"] = $relatedClass;
				}

				$data["description"] = $markdown->transformMarkdown($data["description"]);

				unset($data['id']);
				$statuses[] = $data;
			}

			return array("statuses" => $statuses);
		}

		/**
		 * @url                 /status/:code
		 * @verbs               GET
		 * @template            single.twig
		 * @render              nonAJAX
		 * @condition           code            \d{3}
		 */
		public function getStatus()
		{
			$req = $this->getRequest();
			$code = $req->params["code"];

			$statuses = $this->getStatuses($code);
			return array("title" => "$code", "data" => $statuses["statuses"]);
		}

		/**
		 * @url                 /theme/:file
		 * @verbs               GET
		 * @condition           file        .+
		 */
		public function getFile()
		{
			$req = $this->getRequest();
			$file = $req->params["file"];
			$themePath = $this->getApp()->config("theme.path");

			$mimeType = "text/html";
			$extension = substr($file, strrpos($file, ".") + 1);

			switch($extension)
			{
				case "js":
					$mimeType = "text/javascript";
					break;
				case "css":
					$mimeType = "text/css";
					break;
			}

			$this->getApp()->contentType($mimeType);
			echo file_get_contents($themePath.DIRECTORY_SEPARATOR.$file);
		}
	}
