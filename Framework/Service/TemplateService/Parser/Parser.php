<?php

//プロトタイプのため、nestして分解することができない
class Parser
{
    const END = "/";
    const SINGLE_TAG_FLAG = "single";
    const WRAP_TAG_FLAG = "wrap";
    
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
        "stop" => "}-->",
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

    public function isSingleTag($tag)
    {
        $tags = $this->getTag();
        return in_array($tag, $tags["single"]);
    }

    public function isWrapTag($tag)
    {
        $tags = $this->getTag();
        return in_array($tag, $tags["wrap"]);
    }

    public function findTag($content, $index) {
        $delimiter = $this->getDelimiter();
        $startDelimiter = $delimiter["start"];
        $stopDelimiter = $delimiter["stop"];
        $firstSpace = strpos($content, " ", $index);
        $firstStop = strpos($content, $stopDelimiter, $index);
        if($firstSpace === false && $firstStop === false) {
            throw new \Exception("wrong tag");
        }
        if($firstSpace && $firstSpace < $firstStop) {
            $temp = self::subString($content, $index, $firstSpace);
        } else {
            $temp = self::subString($content, $index, $firstStop);            
        }
        $temp = trim($temp);
        $tagName = str_replace($startDelimiter, "", $temp);
        switch(true) {
        case $this->isSingleTag($tagName):
            $tag = self::subString($content, $index, $firstStop + strlen($stopDelimiter));
            return ["tagName" => $tagName, "tag" => $tag, "tagFlag" => self::SINGLE_TAG_FLAG];
            break;
        case $this->isWrapTag($tagName):
            $endTag = $startDelimiter . self::END . $tagName . $stopDelimiter;
            $endIndex = strpos($content, $endTag, $index);
            if($endTag === false) {
                throw new \Exception(sprintf("not found endTag[%s]", $tagName));
            }
            $tag = self::subString($content, $index, $endIndex + strlen($endTag));
            return ["tagName" => $tagName, "tag" => $tag, "tagFlag" => self::WRAP_TAG_FLAG];
            break;
        default:
            throw new \Exception("invalid tag");
            break;
        }
    }

    public function parseTag($tagData)
    {
        $delimiter = $this->getDelimiter();
        $startDelimiter = $delimiter["start"];
        $stopDelimiter = $delimiter["stop"];
        $tag = $tagData["tag"];
        $tagName = $tagData["tagName"];
        $tagFlag = $tagData["tagFlag"];
        $tagInfo = [];
        $start = $startDelimiter . $tagName;
        switch($tagFlag) {
        case self::SINGLE_TAG_FLAG:
            $info = $this->getTagInfo($tag, $start, $stopDelimiter);
            break;
        case self::WRAP_TAG_FLAG:
            $firstStop = strpos($tag, $stopDelimiter);
            $endTag = $startDelimiter . self::END . $tagName . $stopDelimiter;
            $endTagIndex = strpos($tag, $endTag);
            $startTag = self::subString($tag, 0, $firstStop + strlen($stopDelimiter));
            $tagContent = self::subString($tag, $firstStop + strlen($stopDelimiter), $endTagIndex);
            $info = $this->getTagInfo($startTag, $start, $stopDelimiter);
            $tagContent = trim($tagContent);
            if(!empty($tagContent)) {
                $data = $this->parseContent($tagContent);
                if(isset($data["content"])) {
                    $info["content"] = $data["content"];
                    unset($data["content"]);
                }
                if(!empty($data)) {
                    $info["child"] = $data;
                }
            }
            break;
        }
        $tagInfo[$tagName] = $info;
        return $tagInfo;
    }
    
    public function parseContent($content)
    {
        $delimiter = $this->getDelimiter();
        $startDelimiter = $delimiter["start"];
        $stopDelimiter = $delimiter["stop"];
        $data = [];
        //前から順番に探していく
        do {
            $rest = null;
            $index = strpos($content, $startDelimiter);
            $stopIndex = strpos($content, $stopDelimiter);
            if($stopIndex < $index) {
                throw new \Exception(sprintf("stop[%s] before start[%s]", $stopIndex, $index));
            }
            if($index === false) {
                break;
            }
            //タグまでのコンテンツを取得
            $rest = self::subString($content, 0, $index);
            //タグを取得する
            $tagData = $this->findTag($content, $index);
            $tag = $tagData["tag"];
            $tagName = $tagData["tagName"];
            //タグをパースする
            $tagInfo = $this->parseTag($tagData);          
            //コンテンツからパースしたタグを取り除く
            $content = self::subString($content, $index + strlen($tag), -1);
            //do the tag have some replace info?
            if(isset($tagInfo[$tagName]["replace"])) {
                $replace = $this->makeReplace($tagInfo[$tagName]["replace"]);
                $content = $replace . $content;
                unset($tagInfo[$tagName]["replace"]);
            }
            //do we have rest?
            if($rest !== null) {
                $content = $rest . $content;
            }
            $data = array_merge_recursive($data, $tagInfo);
        } while(true);
        if(!empty($content)) {
            $data["content"] = $content;
        }
        return $data;
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

