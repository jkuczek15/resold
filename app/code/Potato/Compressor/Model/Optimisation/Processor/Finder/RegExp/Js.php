<?php
namespace Potato\Compressor\Model\Optimisation\Processor\Finder\RegExp;

use Potato\Compressor\Model\Optimisation\Processor\Finder\JsInterface;
use Potato\Compressor\Model\Optimisation\Processor\Finder\Result\Raw;
use Potato\Compressor\Model\Optimisation\Processor\Finder\Result\Tag;

class Js extends AbstractRegexp implements JsInterface
{
    protected $needles = array(
        "<script[^>\w]*?>.*?<\/script>",
        "<script[^>]*?src=[^>]+?>.*?<\/script>",
        "<script[^>]*?[\"']text\/javascript[\"'][^>]*?>.*?<\/script>",
        "<script[^>]*?[\"']application\/javascript[\"'][^>]*?>.*?<\/script>",
        "<script[^>]*?[\"']javascript[\"'][^>]*?>.*?<\/script>",
    );

    /** @var HtmlComment */
    protected $htmlCommentFinder = null;

    public function __construct(
        HtmlComment $htmlComment
    ) {
        $this->htmlCommentFinder = $htmlComment;
    }

    /**
     * @param string   $haystack
     * @param null|int $start
     * @param null|int $end
     *
     * @return array
     */
    public function findInline($haystack, $start = null, $end = null)
    {
        $result = $this->findAll($haystack, $start, $end);
        foreach ($result as $key => $tag) {
            /** @var Tag $tag */
            $attributes = $tag->getAttributes();
            if (array_key_exists('src', $attributes)) {
                unset($result[$key]);
            }
        }
        return array_values($result);
    }

    /**
     * @param string   $haystack
     * @param null|int $start
     * @param null|int $end
     *
     * @return array
     */
    public function findExternal($haystack, $start = null, $end = null)
    {
        $result = $this->findAll($haystack, $start, $end);
        foreach ($result as $key => $tag) {
            /** @var Tag $tag */
            $attributes = $tag->getAttributes();
            if (!array_key_exists('src', $attributes)) {
                unset($result[$key]);
            }
        }
        return array_values($result);
    }

    /**
     * @param string   $haystack
     * @param null|int $start
     * @param null|int $end
     *
     * @return array
     */
    public function findAll($haystack, $start = null, $end = null)
    {
        $pattern = "/" . join('|', $this->needles) . "/is";
        $result = $this->findByNeedle($pattern, $haystack, $start, $end);
        $result = $this->excludeTagsWhichWithinHtmlComment($result, $haystack);
        return array_values($result);
    }

    /**
     * @param string $source
     * @param int    $pos
     *
     * @return Tag
     */
    protected function processResult($source, $pos)
    {
        $raw = parent::processResult($source, $pos);
        $result = new Tag(
            $raw->getContent(), $raw->getStart(), $raw->getEnd()
        );
        return $result;
    }

    /**
     * @param array $tagList
     * @param string $haystack
     *
     * @return array
     */
    protected function excludeTagsWhichWithinHtmlComment($tagList, $haystack)
    {
        $htmlCommentList = $this->htmlCommentFinder->findAll($haystack);
        foreach ($tagList as $key => $tag) {
            /** @var Tag $tag */
            $start = $tag->getStart();
            foreach ($htmlCommentList as $htmlComment) {
                /** @var Raw $htmlComment */
                if ($htmlComment->getStart() < $start && $htmlComment->getEnd() > $start) {
                    unset($tagList[$key]);
                    break;
                }
            }
        }
        return $tagList;
    }
}