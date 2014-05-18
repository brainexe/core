<?php

namespace Matze\Core\Twig\PiwikExtension;

use Matze\Core\Traits\ConfigTrait;
use Twig_Token;
use Twig_TokenParser;

/**
 * @Service(public=false)
 */
class PiwikExtensionTokenParser extends Twig_TokenParser {

	/**
	 * @var string
	 */
	private $_piwik_site;

	/**
	 * @var integer
	 */
	private $_piwik_id;

	/**
	 * @Inject({"%piwik.site%", "%piwik.id%"});
	 */
	public function __construct($piwik_site, $piwik_id) {
		$this->_piwik_site = $piwik_site;
		$this->_piwik_id = $piwik_id;
	}

	/**
	 * {@inheritdoc}
	 */
	public function parse(Twig_Token $token) {
		$this->parser->getExpressionParser()->parseExpression();
		$this->parser->getStream()->expect(\Twig_Token::BLOCK_END_TYPE);

		return new \Twig_Node_Text($this->_getContent(), $token->getLine());
	}

	/**
	 * {@inheritdoc}
	 */
	public function getTag() {
		return 'piwik';
	}

	/**
	 * @return string
	 */
	private function _getContent() {
		if (empty($this->_piwik_site) || empty($this->_piwik_id)) {
			return '';
		}

		return sprintf(<<<'TAG'
			<script type="text/javascript">
			  var _paq = _paq || [];
			  (function() {
				var u="//%s/";
				_paq.push(["trackPageView"], ["enableLinkTracking"], ["setTrackerUrl", u+"piwik.php"], ["setSiteId", "%s"]);
				var d=document, g=d.createElement("script"), s=d.getElementsByTagName("script")[0]; g.type="text/javascript";
				g.defer=true; g.async=true; g.src=u+"piwik.js"; s.parentNode.insertBefore(g,s);
			  })();
			</script>
TAG
		, $this->_piwik_site, $this->_piwik_id);
	}
}