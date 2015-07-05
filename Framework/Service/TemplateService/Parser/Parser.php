<?php

namespace Framework\Service\TemplateService\Parser;
use Framework\Service\TemplateService\Parser\Interfaces\TagInterface;
use Framework\Service\TemplateService\Parser\Collection;
use Exception;

class Parser
{
    const GLOBAL_BLOCK = "common";
    const END = "/";
    const SINGLE_TAG_FLAG = "single";
    const WRAP_TAG_FLAG = "wrap";
    
    /**
     * ����p�^�O
     * @var mixed $tag 
     * @access private
     * @link
     */
    public $tag = [];

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
     * tpl�f�[�^
     * @api
     * @var array $data 
     * @access private
     * @link
     */
    private $data = [];

    /**
     *
     * @api
     * @var mixed $collection 
     * @access private
     * @link
     */
    private $collection = null;

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
        $tags = $this->getTag();
        $Tag = null;
        if(isset($tags[$tagName])) {
            $tagClass = $tags[$tagName];
            $Tag = new $tagClass();
        }
        switch(true) {
        case $Tag && $Tag::isSingleTag:
            $raw = self::subString($content, $index, $firstStop + strlen($stopDelimiter));
            break;
        case $Tag && $Tag::isWrapTag:
            $raw = $this->findWrapTag($tagName, $content, $index);
            break;
        default:
            throw new \Exception(sprintf("invalid tag[%s]", $tagName));
            break;
        }
        $Tag->setName($tagName);
        $Tag->setRaw($raw);
        return $Tag;
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
    final private function parseTag($Tag)
    {
        switch(true) {
        case $Tag::isSingleTag:
            $delimiter = $this->getDelimiter();
            $startDelimiter = $delimiter["start"];
            $stopDelimiter = $delimiter["stop"];
            $start = $startDelimiter . $Tag->getName();
            $attrs = $this->getTagInfo($Tag->getRaw(), $start, $stopDelimiter);
            $Tag->setAttrs($attrs);
            break;
        case $Tag::isWrapTag:
            $Tag = $this->parseWrapTag($Tag);
            break;
        }
        return $Tag->onParse($this);
    }

    /**
     * ��荞�݃^�O���p�[�X����
     *
     */
    private function parseWrapTag($Tag)
    {
        $tagName = $Tag->getName();
        $raw = $Tag->getRaw();
        $delimiter = $this->getDelimiter();
        $startDelimiter = $delimiter["start"];
        $stopDelimiter = $delimiter["stop"];
        $start = $startDelimiter . $tagName;
        $firstStop = strpos($raw, $stopDelimiter);
        $endTag = $startDelimiter . self::END . $tagName . $stopDelimiter;
        $startTag = self::subString($raw, 0, $firstStop + strlen($stopDelimiter));
        $tagContent = self::subString($raw, $firstStop + strlen($stopDelimiter), strlen($raw) - strlen($endTag));
        $attrs = $this->getTagInfo($startTag, $start, $stopDelimiter);
        $Tag->setAttrs($attrs);
        $tagContent = trim($tagContent);
        if(!empty($tagContent)) {
            $data = $this->parseContent($tagContent);
            if(isset($data["content"])) {
                $Tag->setContent($data["content"]);
                unset($data["content"]);
            }
            if(!empty($data)) {
                $Tag->setChild($data);
            }
        }
        return $Tag;
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
        $data = $this->parseContent($content);
        $topTag = $data[0];
        $this->getCollection()->addTag($topTag);
        return $topTag;
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
            $Tag = $this->findTag($content, $index);
            //�R���e���c����p�[�X�����^�O����菜��
            $content = self::subString($content, $index + strlen($Tag->getRaw()), -1);
            //�^�O���p�[�X����
            $Tag = $this->parseTag($Tag);
            if($Tag instanceof TagInterface) {
                //do the tag have some replace info?
                if($Tag->getReplace()) {
                    $replace = $this->makeReplace($Tag->getReplace());
                    $content = $replace . $content;
                }
                $data[] = $Tag;
            } else if($Tag !== null) {
                throw new Exception(sprintf("except for Tag or Null in [%s]", get_class($Tag)));
            }
            //do we have rest?
            if($rest !== null) {
                $content = $rest . $content;
            }
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
        //if we have "|", convert it to "pipe[]="
        $target = str_replace("|", " pipe[]=", $target);
        //=���̋󔒂���菜��
        while(strpos($target, " =") !== false) {
            $target = str_replace(" =", "=", $target);
        }
        while(strpos($target, "= ") !== false) {
            $target = str_replace("= ", "=", $target);
        }
        //convert repeats space to single space
        while(strpos($target, "  ") !== false) {
            $target = str_replace("  ", " ", $target);
        }
        //�󔒂ŕ������Ă���&�ōČ������āAquery��������d�グ��
        $target = str_replace(" ", "&", $target);
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
    
    final private function tagSetf($info)
    {
        $data = $info["attrs"];
        $data = array_merge($this->getData(), $data);
        $this->setData($data);
        return [];
    }

    /**
     * 
     * @api
     * @param mixed $collection
     * @return mixed $collection
     * @link
     */
    public function setCollection (Collection $collection)
    {
        return $this->collection = $collection;
    }

    /**
     * 
     * @api
     * @return mixed $collection
     * @link
     */
    public function getCollection ()
    {
        if($this->collection === null) {
            $this->collection = new Collection;
        }
        return $this->collection;
    }
}

