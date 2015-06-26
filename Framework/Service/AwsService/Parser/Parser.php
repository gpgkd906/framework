<?php

//プロトタイプのため、nestして分解することができない
class Parser
{
    /**
     * htmlを分解した情報
     * @var mixed $data 
     * @access private
     * @link
     */
    private $data = [];
    
    /**
     * 対象html情報
     * @var mixed $content 
     * @access private
     * @link
     */
    private $content = null;

    /**
     * 分解用タグ
     * @var mixed $tag 
     * @access private
     * @link
     */
    private $tag = [
        //singletagは{set layout=xxxx template=xxxx}のように使う
        "single" => [
            "set",
        ],
        //wraptagは{block name=xxx}yyyyy{/block}のように使う
        "wrap" => [
            "style", "script", "block", "parts"
        ]
    ];

    /**
     * デリミター
     * @var mixed $delimiter 
     * @access private
     * @link
     */
    private $delimiter = [
        "start" => "<!--{",
        "stop" => "}-->"
    ];

    /**
     * 予約字
     * @api
     * @var mixed $keyword 
     * @access private
     * @link
     */
    private $keyword = [
        "view", "child", "content"
    ];

    /**
     * 
     * @param mixed $data
     * @return mixed $data
     * @link
     */
    private function setData ($data)
    {
        return $this->data = $data;
    }

    /**
     * htmlを分解した情報を取得
     * @api
     * @return mixed $data
     * @link
     */
    public function getData ()
    {
        return $this->data;
    }

    /**
     * 対象html情報をセットする
     * @api
     * @param mixed $content
     * @return mixed $content
     * @link
     */
    public function setContent ($content)
    {
        return $this->content = $content;
    }

    /**
     * 対象html情報を取得する
     * @api
     * @return mixed $content
     * @link
     */
    public function getContent ()
    {
        return $this->content;
    }

    /**
     * タグをセットする
     * @api
     * @param mixed $tag
     * @return mixed $tag
     * @link
     */
    public function setTag ($tag)
    {
        return $this->tag = $tag;
    }

    /**
     * タグを取得する
     * @api
     * @return mixed $tag
     * @link
     */
    public function getTag ()
    {
        return $this->tag;
    }

    /**
     * 
     * @api
     * @param mixed $delimiter
     * @return mixed $delimiter
     * @link
     */
    public function setDelimiter ($delimiter)
    {
        return $this->delimiter = $delimiter;
    }

    /**
     * 
     * @api
     * @return mixed $delimiter
     * @link
     */
    public function getDelimiter ()
    {
        return $this->delimiter;
    }

    /**
     * 
     * @api
     * @param mixed $keyword
     * @return mixed $keyword
     * @link
     */
    public function setKeyword ($keyword)
    {
        return $this->keyword = $keyword;
    }

    /**
     * 
     * @api
     * @return mixed $keyword
     * @link
     */
    public function getKeyword ()
    {
        return $this->keyword;
    }

    static public function subString($string, $index, $offset)
    {
        if($offset === -1) {
            $length = strlen($string) - $offset;
        } else {
            $length = $offset - $index;
        }
        return substr($string, $index, $length);
    }

    static public function findTag($content, $index) {
        
        
        
    }
        

    public function parseContent($content)
    {
        $delimiter = $this->getDelimiter();
        $startDelimiter = $delimiter["start"];
        $stopDelimiter = $delimiter["stop"];
        $data = [];
        $rest = [];
        //前から順番に探していく
        $index = false;
        do {
            $index = strpos($content, $startDelimiter);
            if($index === false) {
                break;
            }
            //タグまでのコンテンツを取得
            $rest[] = self::subString($content, $index);
            //タグを取得する
            $tag = self::findTag($content, $index);
            //タグをパースする
            $tagInfo = self::parseTag($tag);
            if(isset($tagInfo["replace"])) {
                $rest[] = $tagInfo["replace"];
            }
            $data = array_merge_recursive($data, $tagInfo);
            //コンテンツからパースしたタグを取り除く
            $content = self::subString($content, $index + strlen($tag));
        } while(true);
        if(!empty($content)) {

            
        }
        
    }
    
    
    
    public function parseContent2($content)
    {
        $tags = $this->getTag();
        $delimiter = $this->getDelimiter();
        $data = [];
        //まずはsingleタグを処理する
        if(!empty($tags["single"])) {
            foreach($tags["single"] as $singleTag) {
                $start = $delimiter["start"] . $singleTag;
                $stop = $delimiter["stop"];
                list($tagInfo, $content) = $this->parseSingleTag($content, $singleTag, $start, $stop);
                if(!empty($tagInfo)) {
                    $data[$singleTag] = $tagInfo;
                }
            }
        }
        //そして回り囲むタグを処理する
        if(!empty($tags["wrap"])) {
            foreach($tags["wrap"] as $wrapTag) {
                $start = $delimiter["start"] . $wrapTag;
                $stop = $delimiter["start"] . "/" . $wrapTag . $delimiter["stop"];
                list($tagInfo, $content) = $this->parseWrapTag($content, $wrapTag, $start, $stop, $delimiter["stop"]);
                if(!empty($tagInfo)) {
                    $data[$wrapTag] = $tagInfo;
                }
            }
        }
        if(!empty($content)) {
            $data["content"] = $content;
        }
        return $data;
    }

    //singletagは{set layout=xxxx template=xxxx}のように使う
    private function parseSingleTag($content, $tag, $start, $stop)
    {
        $tagInfo = [];
        do {
            $index = strpos($content, $start);
            if($index === false) {
                break;
            }
            $offset = strpos($content, $stop) + strlen($stop);
            $target = self::subString($content, $index, $offset);
            $tagInfo = array_merge($tagInfo, $this->getTagInfo($target, $start, $stop));
            $tempContent = [self::subString($content, 0, $index) ];
            if(isset($tagInfo["replace"])) {
                $tempContent[] = $this->makeReplace($tagInfo["replace"]);
            }
            $tempContent[] = self::subString($content, $offset, -1);
            $content = join("", $tempContent);
            $tagInfo = array_merge($tagInfo, $this->getTagInfo($target, $start, $stop));
            //抽出したデータを対応するtag処理に渡す
            $tagProcess = "tag" . ucfirst($tag);
            if(is_callable([$this, $tagProcess])) {
                $tagInfo = call_user_func([$this, $tagProcess], $tagInfo);
            }
        } while(true);
        return [$tagInfo, $content];
    }

    public function getTagInfo($tag, $start, $stop)
    {
        //前後のタグを取り除く
        $target = trim(str_replace([$start, $stop], "", $tag));
        //=回りの空白を取り除く
        $target = preg_replace("/\s+=\s+/", "=", $target);
        //空白で分割してから&で再結合して、query文字列を仕上げる
        $target = join("&", preg_split("/\s+/", $target));
        //queryをparseしてパラメタを抽出
        parse_str($target, $data);
        return $data;
    }
    
    private function parseWrapTag($content, $tag, $start, $stop, $stopDelimiter)
    {
        $tagData = [];
        do {
            $index = strpos($content, $start);
            if($index === false) {
                break;
            }
            $offset = strpos($content, $stop) + strlen($stop);
            $target = self::subString($content, $index, $offset);
            list($tagInfo, $tagContent) = $this->getWrapTagInfo($target, $start, $stop, $stopDelimiter);
            $tempContent = [self::subString($content, 0, $index) ];
            if(isset($tagInfo["replace"])) {
                $tempContent[] = $this->makeReplace($tagInfo["replace"]);
            }
            $tempContent[] = self::subString($content, $offset, -1);
            $content = join("", $tempContent);
            //抽出したデータを対応するtag処理に渡す
            $tagProcess = "tag" . ucfirst($tag);
            if(is_callable([$this, $tagProcess])) {
                list($tagInfo, $content) = call_user_func_array([$this, $tagProcess], [$tagInfo, $content]);
            }
            $data = [
                "tagInfo" => $tagInfo,
            ];
            if(!empty($tagContent)) {
                $subData = $this->parseContent($tagContent);
                if(isset($subData["content"])) {
                    $data["content"] = $subData["content"];
                    unset($subData);
                }
                if(!empty($subData)) {
                    $data["child"] = $subData;
                }
            }
            $tagData[] = $data;
        } while(true);
        return [$tagData, $content];
    }

    private function getWrapTagInfo($content, $start, $stop, $stopDelimiter)
    {
        $index = strpos($content, $start);
        if($index === false) {
            return false;
        }
        $offset = strpos($content, $stopDelimiter) + strlen($stopDelimiter);
        $target = self::subString($content, $index, $offset);
        $targetInfo = $this->getTagInfo($target, $start, $stopDelimiter);
        $content = trim(substr($content, $offset, -1 * strlen($stop)));
        return [$targetInfo, $content];
    }
    
    public function getTagInfo2($tag, $start, $stop)
    {
        //前後のタグを取り除く
        $target = trim(str_replace([$start, $stop], "", $tag));
        //=回りの空白を取り除く
        $target = preg_replace("/\s+=\s+/", "=", $target);
        //空白で分割してから&で再結合して、query文字列を仕上げる
        $target = join("&", preg_split("/\s+/", $target));
        //queryをparseしてパラメタを抽出
        parse_str($target, $data);
        return $data;
    }

    private function makeReplace($replaceInfo)
    {
        if(strpos($replaceInfo, "parser:") === 0) {
            $replaceCall = str_replace("parser:", "", $replaceInfo);
            if(is_callable([$this, $replaceCall])) {
                return call_user_func([$this, $replaceCall]);
            } else {
                throw new \Exception("指定した置換処理はアクセスできません");
            }
        } else {
            return "<?php echo " . $replaceInfo . " ?>";
        }
    }
}

