<?php
namespace App\Support;

/** Pembersih HTML berbasis whitelist (tanpa dependensi) untuk isi dari editor admin. */
class Html
{
    public static function clean(?string $html): string
    {
        if (! trim((string) $html)) return '';
        $ok = ['p','br','b','strong','i','em','u','del','ul','ol','li','h1','h2','h3','h4','blockquote','a','img',
               'table','thead','tbody','tr','th','td','div','span','figure','figcaption','pre'];
        $d = new \DOMDocument();
        libxml_use_internal_errors(true);
        $d->loadHTML('<?xml encoding="utf-8"?><body>'.$html.'</body>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $walk = function (\DOMNode $n) use (&$walk, $ok) {
            foreach (iterator_to_array($n->childNodes) as $c) {
                if ($c instanceof \DOMElement) {
                    if (! in_array($c->nodeName, $ok)) { $n->removeChild($c); continue; }
                    foreach (iterator_to_array($c->attributes) as $a) {
                        $keep = in_array($a->name, ['href','src','alt','title'])
                            && ! preg_match('/^\s*(javascript|data|vbscript):/i', $a->value);
                        if (! $keep) $c->removeAttribute($a->name);
                    }
                    if ($c->nodeName === 'a') $c->setAttribute('rel', 'noopener nofollow');
                    $walk($c);
                } elseif (! ($c instanceof \DOMText)) {
                    $n->removeChild($c);
                }
            }
        };
        $body = $d->getElementsByTagName('body')->item(0);
        if (! $body) return '';
        $walk($body);
        $out = '';
        foreach ($body->childNodes as $c) $out .= $d->saveHTML($c);
        return $out;
    }
}
