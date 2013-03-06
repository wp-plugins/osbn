<?php
/*
 * [Plugin Name: follow-my-friends]
 * Description: Remove nofollow attributes from selected comment links.
 * Version: 1
 * Author: Sebastian Gaul <sebastian@mgvmedia.com>
 * Author URI: http://sgaul.de
 * 
 * 
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/*if (function_exists('add_filter')) {
	add_filter('get_comment_author_link', 'fmy_removeChosenSingleQuotedNofollowAttributesFromText');
	add_filter('get_comment_text', 'fmy_removeChosenNofollowAttributesFromText');
}

function fmy_removeChosenNofollowAttributesFromText($text, $attributeDelimiter = '"') {
	$fmf = new FollowMyFriends(
		new FMF_AnchorStartingTagFactory($attributeDelimiter)
	);
	return $fmf->removeNofollowFromFriendlyLinks($text);
}

function fmy_removeChosenSingleQuotedNofollowAttributesFromText($text) {
	return fmy_removeChosenNofollowAttributesFromText($text, "'");
}*/

class FollowMyFriends {

	public $friendlyUrls = array();

	private $finder;
	private $tagFactory;

	public function __construct($tagFactory) {
		$this->finder = new FMF_AnchorTagFinder();
		$this->tagFactory = $tagFactory;
	}

	public function removeNofollowFromFriendlyLinks($text) {
		$tagStrings = $this->finder->findTagsInText($text);
		foreach ($tagStrings as $tagString) {
			$tag = $this->tagFactory->getInstance($tagString);
			foreach ($this->friendlyUrls as $url) {
				if (strpos($tag->getHref(), $url) === 0) {
					$tag->setRel('external');
					$text = str_replace($tagString, $tag->__toString(), $text);
				}
			}
		}
		return $text;
	}

}

class FMF_AnchorTagFinder {

	/**
	 * Find tags, e.g. "<a href="">"
	 */
	public function findTagsInText($text) {
		$tags = array();
		$parts = explode('<', $text);
		foreach ($parts as $i => $part) {
			if ($i == 0) {
				continue;
			}
			if ($this->startsWith($part, 'a ')) {
				$tag = '<' . $part;
				$tag = $this->extractBeginningTag($tag);
				if ($tag !== null) {
					$tags[] = $tag;
				}
			}
		}
		return $tags;
	}

	public function startsWith($haystack, $needle) {
		return (strpos($haystack, $needle) === 0);
	}

	/**
	 * Get the tag from the beginning of a string
	 * "<a href>Foo</a>..." => "<a href>"
	 */
	public function extractBeginningTag($htmlFramgent) {
		$parts = explode('>', $htmlFramgent);
		if (count($parts) > 1) {
			return $parts[0] . '>';
		} else {
			return null;
		}
	}

}

class FMF_AnchorStartingTagFactory {
	private $attributeDelimiter;

	public function __construct($attributeDelimiter) {
		$this->attributeDelimiter = $attributeDelimiter;
	}

	public function getInstance($tagString) {
		return new FMF_AnchorStartingTag($tagString, $this->attributeDelimiter);
	}
}

class FMF_AnchorStartingTag {

	private $tag;
	private $attributeDelimiter;

	public function __construct($tag, $attributeDelimiter) {
		$this->tag = $tag;
		$this->attributeDelimiter = $attributeDelimiter;
	}

	public function getHref() {
		if (preg_match(
			'/href=' . $this->attributeDelimiter . '([^' . $this->attributeDelimiter . ']*)' . $this->attributeDelimiter . '/', 
			$this->tag, $result)) {
			return $result[1];
		}
		return null;
	}

	public function getRel() {
		if (preg_match(
			'/rel=' . $this->attributeDelimiter . '([^' . $this->attributeDelimiter . ']*)' . $this->attributeDelimiter . '/',
			$this->tag, $result)) {
			return $result[1];
		}
		return null;
	}

	public function setRel($value) {
		if ($value != null) {
			$value = 'rel=' . $this->attributeDelimiter . $value . $this->attributeDelimiter;
		}
		$this->tag = str_replace(
			'rel=' . $this->attributeDelimiter . $this->getRel() . $this->attributeDelimiter,
			$value,
			$this->tag
		);
	}

	public function __toString() {
		return $this->tag;
	}

}
