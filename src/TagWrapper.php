<?php

namespace Peidgnad\TagWrapper;

class TagWrapper
{
    public $string;

    public $keywords;

    public $prepend;

    public $append;

    public $ignoreTags = ['a'];

    public $ignoreIndexes = [];

    public function __construct($keywords, $string)
    {
        $this->keywords = !is_array($keywords) ? [$keywords] : $keywords;
        $this->string = $string;
    }

    public static function find($keywords, $string)
    {
        return new self($keywords, $string);
    }

    public function wrapWith($prepend, $append)
    {
        $this->prepend = $prepend;
        $this->append = $append;

        return $this;
    }

    public function ignoreHtmlTags($tags)
    {
        $this->ignoreTags = $tags;
    }

    public function ignoreIndex($index)
    {
        $this->ignoreIndexes = !is_array($index) ? [$index] : $index;

        return $this;
    }

    public function exec($entities = true)
    {
        if ($entities) {
            $this->keywords = array_unique(array_merge($this->keywords, array_map(static function ($keyword) {
                return htmlentities($keyword);
            }, $this->keywords)));
        }

        usort($this->keywords, static function ($a, $b) {
            return strlen($b) - strlen($a);
        });

        // Replace all html tags with harmless string.
        $pattern = '/<([a-zA-Z]+)\s?([a-zA-Z0-9\s\-="\']+)?(>|\/>)/mui';

        $string = preg_replace_callback($pattern, static function ($match) {
            return str_replace($match[2], implode('', array_fill(0, strlen($match[2]), 'a')), $match[0]);
        }, $this->string);

        // Find keywords and wrap it.
        $keywords = implode('|', $this->keywords);
        $ignoreTags = implode('|', $this->ignoreTags);

        $pattern = "/\b($keywords)\b(?![^<]*<\/($ignoreTags)>)/imu";

        preg_match_all($pattern, $string, $matches, PREG_OFFSET_CAPTURE);

        $position = 0;
        $indexes = [];
        $positions = [];

        foreach ($matches[0] as $index => $match) {
            if (in_array($index, $this->ignoreIndexes)) {
                continue;
            }

            $indexes[$index] = $match[0];
            $positions[$match[1]] = $match[0];

            // Prepend to string.
            $prepend = $this->prepend;

            if (is_callable($prepend)) {
                $prepend = $prepend($match[0]);
            }

            $this->string = substr_replace($this->string, $prepend, $match[1] + $position, 0);

            $position += strlen($prepend);

            // Append to string.
            $append = $this->append;

            if (is_callable($append)) {
                $append = $append($match[0]);
            }

            $this->string = substr_replace($this->string, $append, $match[1] + $position + strlen($match[0]), 0);

            $position += strlen($append);
        }

        return (new Result($this->string, $indexes, $positions));
    }
}
