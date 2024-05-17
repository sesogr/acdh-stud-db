<?php
    function out ($string, $markIllegible = false) {
        echo $markIllegible
            ? preg_replace('/x{2,}/i', '<span title="unleserlich" class="illegible">###</span>', htmlspecialchars($string))
            : htmlspecialchars($string);
    }
