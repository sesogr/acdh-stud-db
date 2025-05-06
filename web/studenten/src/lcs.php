<?php
    /**
     * Computes the longest common substring of two strings.
     *
     * @param string $one
     * @param string $two
     * @return string
     */
    function longest_common_substring($one, $two)
    {
        $oneLen = strlen($one);
        $twoLen = strlen($two);

        // make sure that $one is the shorter string by swapping variables if necessary
        if ($twoLen < $oneLen) {
            list($one, $two, $oneLen, $twoLen) = array($two, $one, $twoLen, $oneLen);
        }

        // search for the whole $one within $two, then reduce search length one by one
        // when a match is found it will be the longest, since shorter matches would occur later
        $searchLen = $oneLen;
        while ($searchLen > 0) {

            // with the given search length walk through all of $one from left to right
            for ($searchPos = 0, $max = $oneLen - $searchLen; $searchPos <= $max; $searchPos++) {
                $substr = substr($one, $searchPos, $searchLen);

                // when the current substring can be found in $two, it will be our longest common substring
                if (strpos($two, $substr) !== false) {
                    return $substr;
                }
            }
            $searchLen--;
        }

        // when all that looping did not find a single matching substring, we're left to return an empty string
        return '';
    }
