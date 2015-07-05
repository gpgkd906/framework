<?php

class Parser
{
    const GLOBAL_BLOCK = "global";
    const END = "/";
    const SINGLE_TAG_FLAG = "single";
    const WRAP_TAG_FLAG = "wrap";
    
    /**
     * ����p�^�O
     * @var mixed $tag 
     * @access private
     * @link
     */
    private $tag = [
        //singletag��{setf layout=xxxx template=xxxx}�̂悤�Ɏg��
        "single" => [
            "setf",
        ],
        //wraptag��{block name=xxx}yyyyy{/block}�̂悤�Ɏg��
        "wrap" => [
            "style", "script", "block", "parts", "global"
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
     * ��������id�J�E���g
     * @var mixed $delimiter 
     * @access private
     * @link
     */
    private $id = 0;
    
    /**
     * ��������id�J�E���g�擾
     * @var mixed $delimiter 
     * @access private
     * @link
     */
    public function getId()
    {
        ++$this->id;
        return "tag_" . $this->id;
    }

    /**
     * tpl�f�[�^
     * @api
     * @var array $data 
     * @access private
     * @link
     */
    private $data = [];

    /**
     * tpl�f�[�^�擾
     * @api
     * @param mixed $data
     * @return mixed $data
     * @link
     */
    public function setData ($data)
    {
        return $this->data = $data;
    }

    /**
     * tpl�f�[�^�ǉ�
     * @api
     * @return mixed $data
     * @link
     */
    public function getData ()
    {
        return $this->data;
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
     * �w��ʒu����w��ʒu�܂ł̕�������擾����
     * @api
     * @param string 
     * @param integer
     * @param integer
     * @return string
     */
    static public function subString($string, $index, $offset = -1)
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

    /**
     * �^�O���擾����Anesting�^�O�Ή�
     *
     *
     *
     */
    final private function findTag($content, $index) {
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
            $tag = $this->findWrapTag($tagName, $content, $index);
            return ["tagName" => $tagName, "tag" => $tag, "tagFlag" => self::WRAP_TAG_FLAG];
            break;
        default:
            throw new \Exception("invalid tag");
            break;
        }
    }

    /**
     * ��荞�݃^�O���擾����
     * 
     *
     */
    final private function findWrapTag($tagName, $content, $index)
    {
        $delimiter = $this->getDelimiter();
        $startDelimiter = $delimiter["start"];
        $stopDelimiter = $delimiter["stop"];
        $startTag = $startDelimiter . $tagName;
        $endTag = $startDelimiter . self::END . $tagName . $stopDelimiter;
        $endTagLen = strlen($endTag);
        $offset = $index;
        $contentLen = strlen($content);
        do {
            $endIndex = strpos($content, $endTag, $offset);
            if($endTag === false) {
                throw new \Exception(sprintf("not found endTag[%s]", $tagName));
            }
            $offset = $endIndex + $endTagLen;
            $startCount = substr_count($content, $startTag, 0, $offset);
            $endCount = substr_count($content, $endTag, 0, $offset);
            if($startCount === $endCount) {
                break;
            }
        } while(true);
        $tag = self::subString($content, $index, $endIndex + strlen($endTag));
        return $tag;
    }

    /**
     * �^�O���p�[�X����
     *
     */
    final private function parseTag($tagData)
    {
        $delimiter = $this->getDelimiter();
        $startDelimiter = $delimiter["start"];
        $stopDelimiter = $delimiter["stop"];
        $tag = $tagData["tag"];
        $tagName = $tagData["tagName"];
        $tagFlag = $tagData["tagFlag"];
        $start = $startDelimiter . $tagName;
        $info = [];
        switch($tagFlag) {
        case self::SINGLE_TAG_FLAG:
            $info["attrs"] = $this->getTagInfo($tag, $start, $stopDelimiter);
            break;
        case self::WRAP_TAG_FLAG:
            $info = $this->parseWrapTag($tag, $tagName);
            break;
        }
        if(isset($info["attrs"]["replace"])) {
            $info["replace"] = $info["attrs"]["replace"];
            unset($info["attrs"]["replace"]);
        }
        $info["attrs"]["tag"] = $tagName;
        $tagInfo = $this->tagCall($info);
        return $tagInfo;
    }

    /**
     * ��荞�݃^�O���p�[�X����
     *
     */
    private function parseWrapTag($tag, $tagName)
    {
        $info = [];
        $delimiter = $this->getDelimiter();
        $startDelimiter = $delimiter["start"];
        $stopDelimiter = $delimiter["stop"];
        $start = $startDelimiter . $tagName;
        $firstStop = strpos($tag, $stopDelimiter);
        $endTag = $startDelimiter . self::END . $tagName . $stopDelimiter;
        $startTag = self::subString($tag, 0, $firstStop + strlen($stopDelimiter));
        $tagContent = self::subString($tag, $firstStop + strlen($stopDelimiter), strlen($tag) - strlen($endTag));
        $info["attrs"] = $this->getTagInfo($startTag, $start, $stopDelimiter);
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
        return $info;
    }

    /**
     * �p�[�X�����G���g���[
     *
     */
    public function parse($content)
    {        
        $delimiter = $this->getDelimiter();
        $startDelimiter = $delimiter["start"];
        $stopDelimiter = $delimiter["stop"];
        $globalStart = $startDelimiter . self::GLOBAL_BLOCK . $stopDelimiter;
        $globalEnd = $startDelimiter . self::END . self::GLOBAL_BLOCK . $stopDelimiter;
        $content = $globalStart . $content . $globalEnd;
        return $this->parseContent($content);
    }

    /**
     * �^�O�p�[�X����
     * 
     */
    final public function parseContent($content)
    {
        $delimiter = $this->getDelimiter();
        $startDelimiter = $delimiter["start"];
        $stopDelimiter = $delimiter["stop"];
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
            if(isset($tagInfo["replace"])) {
                $replace = $this->makeReplace($tagInfo["replace"]);
                $content = $replace . $content;
                unset($tagInfo["replace"]);
            }
            //do we have rest?
            if($rest !== null) {
                $content = $rest . $content;
            }
            if(empty($tagInfo)) {
                continue;
            }
            $data[] = $tagInfo;
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
        //format space:zenkaku => hankaku
        //$target = str_replace(" ", " ", $target);
        //=���̋󔒂���菜��
        $target = preg_replace("/\s+=\s+/", "=", $target);
        //�󔒂ŕ������Ă���&�ōČ������āAquery��������d�グ��
        $target = join("&", preg_split("/\s+/", $target));
        //query��parse���ăp�����^�𒊏o
        parse_str($target, $data);
        //id���w�肳��ĂȂ��ꍇ�́A��������id��U��
        if(!isset($data["id"])) {
            $data["id"] = $this->getId();
        }
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
    
    final private function tagCall($tagInfo)
    {
        $tagName = $tagInfo["attrs"]["tag"];
        $processer = "tag" . ucfirst($tagName);
        if(is_callable([$this, $processer])) {
            $tagInfo = call_user_func([$this, $processer], $tagInfo);
        }
        return $tagInfo;
    }

    final private function tagSetf($info)
    {
        $data = $info["attrs"];
        $data = array_merge($this->getData(), $data);
        $this->setData($data);
        return [];
    }
}

