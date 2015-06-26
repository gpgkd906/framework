<?php

//�v���g�^�C�v�̂��߁Anest���ĕ������邱�Ƃ��ł��Ȃ�
class Parser
{
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
        "stop" => "}-->"
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

    static public function findTag($content, $index) {
        
        
        
    }
        

    public function parseContent($content)
    {
        $delimiter = $this->getDelimiter();
        $startDelimiter = $delimiter["start"];
        $stopDelimiter = $delimiter["stop"];
        $data = [];
        $rest = [];
        //�O���珇�ԂɒT���Ă���
        $index = false;
        do {
            $index = strpos($content, $startDelimiter);
            if($index === false) {
                break;
            }
            //�^�O�܂ł̃R���e���c���擾
            $rest[] = self::subString($content, $index);
            //�^�O���擾����
            $tag = self::findTag($content, $index);
            //�^�O���p�[�X����
            $tagInfo = self::parseTag($tag);
            if(isset($tagInfo["replace"])) {
                $rest[] = $tagInfo["replace"];
            }
            $data = array_merge_recursive($data, $tagInfo);
            //�R���e���c����p�[�X�����^�O����菜��
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
        //�܂���single�^�O����������
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
        //�����ĉ��͂ރ^�O����������
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

    //singletag��{set layout=xxxx template=xxxx}�̂悤�Ɏg��
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
            //���o�����f�[�^��Ή�����tag�����ɓn��
            $tagProcess = "tag" . ucfirst($tag);
            if(is_callable([$this, $tagProcess])) {
                $tagInfo = call_user_func([$this, $tagProcess], $tagInfo);
            }
        } while(true);
        return [$tagInfo, $content];
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
            //���o�����f�[�^��Ή�����tag�����ɓn��
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

