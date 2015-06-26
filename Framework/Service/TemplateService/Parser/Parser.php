<?php

//�v���g�^�C�v�̂��߁Anest���ĕ������邱�Ƃ��ł��Ȃ�
class Parser
{
    const END = "/";
    const SINGLE_TAG_FLAG = "single";
    const WRAP_TAG_FLAG = "wrap";
    
    /**
     * html�𕪉��������
     * @var mixed $data 
     * @access private
     * @link
     */
    private $data = [];
    
    /**
     * �Ώ�html���
     * @var mixed $content 
     * @access private
     * @link
     */
    private $content = null;

    /**
     * ����p�^�O
     * @var mixed $tag 
     * @access private
     * @link
     */
    private $tag = [
        //singletag��{set layout=xxxx template=xxxx}�̂悤�Ɏg��
        "single" => [
            "set",
        ],
        //wraptag��{block name=xxx}yyyyy{/block}�̂悤�Ɏg��
        "wrap" => [
            "style", "script", "block", "parts"
        ]
    ];

    /**
     * �f���~�^�[
     * @var mixed $delimiter 
     * @access private
     * @link
     */
    private $delimiter = [
        "start" => "<!--{",
        "stop" => "}-->",
    ];

    /**
     * �\��
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
     * html�𕪉����������擾
     * @api
     * @return mixed $data
     * @link
     */
    public function getData ()
    {
        return $this->data;
    }

    /**
     * �Ώ�html�����Z�b�g����
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
     * �Ώ�html�����擾����
     * @api
     * @return mixed $content
     * @link
     */
    public function getContent ()
    {
        return $this->content;
    }

    /**
     * �^�O���Z�b�g����
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
     * �^�O���擾����
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
        //�O���珇�ԂɒT���Ă���
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
            //�^�O�܂ł̃R���e���c���擾
            $rest = self::subString($content, 0, $index);
            //�^�O���擾����
            $tagData = $this->findTag($content, $index);
            $tag = $tagData["tag"];
            $tagName = $tagData["tagName"];
            //�^�O���p�[�X����
            $tagInfo = $this->parseTag($tagData);          
            //�R���e���c����p�[�X�����^�O����菜��
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
        //�O��̃^�O����菜��
        $target = trim(str_replace([$start, $stop], "", $tag));
        //=���̋󔒂���菜��
        $target = preg_replace("/\s+=\s+/", "=", $target);
        //�󔒂ŕ������Ă���&�ōČ������āAquery��������d�グ��
        $target = join("&", preg_split("/\s+/", $target));
        //query��parse���ăp�����^�𒊏o
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
                throw new \Exception("�w�肵���u�������̓A�N�Z�X�ł��܂���");
            }
        } else {
            return "<?php echo " . $replaceInfo . " ?>";
        }
    }
}

