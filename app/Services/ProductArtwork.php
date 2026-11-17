<?php
declare(strict_types=1);

/** Builds a distinct, lightweight catalog illustration for products without a photo. */
final class ProductArtwork
{
    public static function ensure(string $slug, string $name, string $model, string $category): string
    {
        $slug = preg_replace('/[^a-z0-9-]/', '', strtolower($slug)) ?: 'producto';
        $relative = 'assets/images/products/' . $slug . '.svg';
        $file = ROOT . '/public/' . $relative;
        if (!is_file($file)) {
            $dir = dirname($file);
            if (!is_dir($dir)) mkdir($dir, 0775, true);
            file_put_contents($file, self::svg($slug, $name, $model, $category), LOCK_EX);
        }
        return $relative;
    }

    private static function svg(string $slug, string $name, string $model, string $category): string
    {
        $palette = ['#7352dc', '#2875d8', '#0f8f91', '#cc6a32', '#4858ac', '#8c4f91'];
        $accent = $palette[abs(crc32($slug)) % count($palette)];
        $esc = static fn(string $s): string => htmlspecialchars($s, ENT_QUOTES | ENT_XML1, 'UTF-8');
        $label = $esc(mb_strimwidth($name, 0, 34, '…', 'UTF-8'));
        $sub = $esc(mb_strimwidth($model, 0, 30, '…', 'UTF-8'));
        $hash = strtoupper(substr(hash('sha256', $slug), 0, 6));
        $device = match ($category) {
            'portatiles' => '<g transform="translate(0 4)"><path d="M205 126h230l18 152H187z" fill="#18202b"/><path d="M218 139h204v121H203z" fill="url(#screen)"/><path d="M188 278h265l27 18q-2 12-17 12H174q-14 0-14-11z" fill="#697386"/><path d="M265 281h113l-12 8h-91z" fill="#c6ccd7"/></g>',
            'pc-escritorio' => '<g><rect x="245" y="95" width="150" height="225" rx="20" fill="#202632"/><rect x="259" y="111" width="122" height="190" rx="12" fill="#111722"/><circle cx="320" cy="171" r="39" fill="#252e3b" stroke="url(#ring)" stroke-width="7"/><circle cx="320" cy="171" r="12" fill="#7a8799"/><circle cx="320" cy="229" r="39" fill="#252e3b" stroke="url(#ring)" stroke-width="7"/><circle cx="320" cy="229" r="12" fill="#7a8799"/><circle cx="367" cy="128" r="4" fill="' . $accent . '"/></g>',
            'componentes' => '<g transform="rotate(-8 320 205)"><rect x="198" y="145" width="244" height="122" rx="15" fill="#232b38"/><rect x="215" y="160" width="101" height="90" rx="10" fill="#141b25" stroke="#697386" stroke-width="3"/><path d="M228 173h75v64h-75z" fill="url(#screen)"/><path d="M334 169h85v18h-85zm0 30h85v18h-85zm0 30h55v16h-55z" fill="#8b94a3"/><path d="M190 162h-18v18h18m0 24h-18v18h18m264-60h18v18h-18m0 24h18v18h-18" fill="none" stroke="' . $accent . '" stroke-width="6"/></g>',
            'perifericos' => '<g><rect x="186" y="150" width="268" height="121" rx="20" fill="#222a36"/><rect x="203" y="166" width="234" height="82" rx="11" fill="#303a49"/>' . self::keys($accent) . '<path d="M252 291q0-49 68-49t68 49v26h-25v-25q0-24-43-24t-43 24v25h-25z" fill="#1e2632" stroke="#667183" stroke-width="4"/></g>',
            'accesorios' => '<g><rect x="253" y="111" width="134" height="202" rx="25" fill="#242c39"/><rect x="265" y="127" width="110" height="161" rx="17" fill="#eef1f6"/><rect x="274" y="141" width="92" height="130" rx="10" fill="url(#screen)"/><circle cx="320" cy="298" r="5" fill="#9099a8"/><path d="M187 214h43m180 0h43" stroke="' . $accent . '" stroke-width="12" stroke-linecap="round"/><circle cx="320" cy="206" r="35" fill="none" stroke="#fff" stroke-opacity=".7" stroke-width="2"/></g>',
            'redes' => '<g><path d="M211 229q109-119 218 0" fill="none" stroke="' . $accent . '" stroke-width="12" stroke-linecap="round"/><path d="M248 258q72-75 144 0" fill="none" stroke="' . $accent . '" stroke-width="12" stroke-linecap="round" opacity=".75"/><path d="M282 286q38-38 76 0" fill="none" stroke="' . $accent . '" stroke-width="12" stroke-linecap="round" opacity=".5"/><rect x="210" y="295" width="220" height="35" rx="12" fill="#222a36"/><circle cx="250" cy="312" r="4" fill="#8ef0bd"/><circle cx="278" cy="312" r="4" fill="#8ef0bd"/><circle cx="306" cy="312" r="4" fill="#8ef0bd"/></g>',
            'monitores' => '<g><rect x="170" y="105" width="300" height="180" rx="17" fill="#202834"/><rect x="182" y="117" width="276" height="156" rx="9" fill="url(#screen)"/><path d="M320 285v31m-55 12h110" stroke="#697386" stroke-width="12" stroke-linecap="round"/><path d="M208 245l64-63 51 43 48-66 67 81" fill="none" stroke="#fff" stroke-opacity=".72" stroke-width="5"/></g>',
            'audio-video' => '<g><path d="M215 230v-35q0-106 105-106t105 106v35h-30v-35q0-76-75-76t-75 76v35z" fill="#252d3a" stroke="#667183" stroke-width="4"/><rect x="190" y="210" width="58" height="112" rx="25" fill="#202834"/><rect x="392" y="210" width="58" height="112" rx="25" fill="#202834"/><rect x="200" y="222" width="38" height="84" rx="17" fill="' . $accent . '" opacity=".75"/><rect x="402" y="222" width="38" height="84" rx="17" fill="' . $accent . '" opacity=".75"/></g>',
            'gaming' => '<g><path d="M211 183q9-31 44-31h130q35 0 44 31l22 86q7 29-21 37-17 5-31-13l-34-38h-90l-34 38q-14 18-31 13-28-8-21-37z" fill="#232b38" stroke="#707b8e" stroke-width="5"/><path d="M253 202v52m-26-26h52" stroke="#eef1f6" stroke-width="11" stroke-linecap="round"/><circle cx="375" cy="213" r="8" fill="' . $accent . '"/><circle cx="399" cy="238" r="8" fill="#65d3bb"/><circle cx="375" cy="261" r="8" fill="#f0bd5c"/></g>',
            'usados' => '<g><rect x="190" y="118" width="260" height="168" rx="16" fill="#252e3a"/><rect x="202" y="130" width="236" height="143" rx="8" fill="url(#screen)"/><path d="M180 287h280l20 18q-2 12-17 12H177q-15 0-17-12z" fill="#8992a1"/><path d="M220 298h200" stroke="#d7dbe3" stroke-width="3"/><circle cx="415" cy="144" r="13" fill="#fff" fill-opacity=".23"/><path d="M406 144l7 7 13-15" fill="none" stroke="#fff" stroke-width="3"/></g>',
            default => '<g><rect x="210" y="135" width="220" height="150" rx="20" fill="#252d3a"/><rect x="225" y="150" width="190" height="120" rx="12" fill="url(#screen)"/></g>',
        };
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 480" role="img" aria-label="' . $label . '"><defs><linearGradient id="bg" x2="1" y2="1"><stop stop-color="#f7f8fc"/><stop offset="1" stop-color="#e9edf5"/></linearGradient><linearGradient id="screen" x1="0" y1="0" x2="1" y2="1"><stop stop-color="' . $accent . '"/><stop offset="1" stop-color="#161b2b"/></linearGradient><linearGradient id="ring"><stop stop-color="' . $accent . '"/><stop offset="1" stop-color="#bba2ff"/></linearGradient></defs><rect width="640" height="480" rx="28" fill="url(#bg)"/><ellipse cx="320" cy="352" rx="190" ry="20" fill="#bcc4d2" opacity=".35"/><circle cx="510" cy="110" r="86" fill="' . $accent . '" opacity=".07"/><circle cx="132" cy="275" r="53" fill="' . $accent . '" opacity=".06"/>' . $device . '<rect x="32" y="378" width="576" height="1" fill="#d7dce6"/><text x="40" y="415" font-family="Arial,sans-serif" font-size="19" font-weight="700" fill="#18202d">' . $label . '</text><text x="40" y="445" font-family="Arial,sans-serif" font-size="14" fill="#657084">' . $sub . ' · ' . $hash . '</text></svg>';
    }

    private static function keys(string $accent): string
    {
        $out = '';
        for ($row = 0; $row < 3; $row++) for ($col = 0; $col < 13; $col++) {
            $x = 212 + $col * 17; $y = 176 + $row * 21;
            $fill = ($row === 0 && $col % 3 === 0) ? $accent : '#aeb6c4';
            $out .= '<rect x="' . $x . '" y="' . $y . '" width="12" height="13" rx="3" fill="' . $fill . '"/>';
        }
        return $out;
    }
}
