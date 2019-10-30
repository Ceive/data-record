<?php
namespace Jungle\Data\Storage\Db\Definition;
use Jungle\Data\Storage\Db\Connection;

/**
 * @author Benjamin Eberlei <kontakt@beberlei.de> (Doctrine)
 */
class SQLParserUtils{
    const POSITIONAL_TOKEN = '\?';
    const NAMED_TOKEN      = '(?<!:):[a-zA-Z_][a-zA-Z0-9_]*';

    // Quote characters within string literals can be preceded by a backslash.
    const ESCAPED_SINGLE_QUOTED_TEXT = "'(?:[^'\\\\]|\\\\'?|'')*'";
    const ESCAPED_DOUBLE_QUOTED_TEXT = '"(?:[^"\\\\]|\\\\"?)*"';
    const ESCAPED_BACKTICK_QUOTED_TEXT = '`(?:[^`\\\\]|\\\\`?)*`';
    const ESCAPED_BRACKET_QUOTED_TEXT = '(?<!\bARRAY)\[(?:[^\]])*\]';

    /**
     * Gets an array of the placeholders in an sql statements as keys and their positions in the query string.
     *
     * Returns an integer => integer pair (indexed from zero) for a positional statement
     * and a string => int[] pair for a named statement.
     * @param string  $statement

     * @return array
     */
    public static function getPlaceholderPositions($statement){
        if (
	        strpos($statement, '?') === false
            && strpos($statement, ':') === false
        ) return [];
        $paramMap = [];$i = 0;
        foreach (self::getUnquotedStatementFragments($statement) as $fragment) {
            preg_match_all("/".self::POSITIONAL_TOKEN."|".self::NAMED_TOKEN."/", $fragment[0], $matches, PREG_OFFSET_CAPTURE);
            foreach ($matches[0] as $placeholder) {
	            if($placeholder[0] === '?'){
		            $paramMap[$placeholder[1] + $fragment[1]] = $i;
		            $i++;
	            }else{
		            $name = substr($placeholder[0], 1, strlen($placeholder[0]));
		            $paramMap[$placeholder[1] + $fragment[1]] = $name;
		            $named_params[] = $name;
	            }
            }
        }
        return $paramMap;
    }

	public static function expand($query, $params, $types, $map = null){
		if(!isset($map)) $map = self::getPlaceholderPositions($query);
		if(!$map){
			return [$query, [], []];
		}
		$ordered_params = $ordered_types = [];
		$array_types = [Connection::PARAM_INT_ARRAY, Connection::PARAM_STR_ARRAY];
		$query_offset = 0;
		foreach($map as $start_position => $name){
			$start_position = $start_position+$query_offset;
			if(!array_key_exists($name, $params)){
				SQLParserUtilsDBALException::missingParam($name);
			}
			$value = $params[$name];
			$type = isset($types[$name])?$types[$name]:null;
			$type_is_auto = !isset($type);
			$value_is_array = is_array($value);
			$type_is_array = in_array($type, $array_types);
			if(is_int($name)){
				$is_positional = true;
				$ph_length = 1;
			}else{
				$ph_length = strlen($name) + 1;
				$is_positional = false;
			}

			if($value_is_array && (!$type_is_array && !$type_is_auto)){
				$ordered_params[] = reset($value);
				$ordered_types[] = $type;
				continue;
			}

			if($type_is_array || ($type_is_auto && $value_is_array) ){
				$type = $type ?: Connection::PARAM_STR_ARRAY;
				if(empty($value)){
					$query = substr_replace($query, 'NULL' ,$start_position,1);
					$query_offset+= 3;
				}else{
					$type = $type===Connection::PARAM_INT_ARRAY?\PDO::PARAM_INT:\PDO::PARAM_STR;
					$value = (array)$value;
					$count = count($value);
					$ordered_params = array_merge($ordered_params, $value);
					$ordered_types = array_merge($ordered_types, array_fill(0,$count,$type));
					$a_string = implode(', ',array_fill(0,$count,'?'));
					$query = substr_replace($query, $a_string ,$start_position,$ph_length);
					$query_offset+= strlen($a_string) - $ph_length ;
				}
			}else{
				if($type_is_auto) $type = \PDO::PARAM_STR;
				if(!$is_positional){
					$query = substr_replace($query, '?' ,$start_position,$ph_length);
					$query_offset+= 1 - $ph_length ;
				}
				$ordered_params[] = $value;
				$ordered_types[] = $type;
			}
		}
		return [$query,$ordered_params, $ordered_types];
	}

	/**
	 * @param $sql
	 * @param $start_pos
	 * @param $length
	 * @param $sign
	 * @param int $plus
	 * @return array
	 */
	public static function normalizeArrayPosition($sql, $start_pos, $length, $sign, & $plus = 0){
		for($i=1;($token = $sql{$start_pos - $i})!=='(';$i++){}
		if($token !== '('){
			$plus++;
			$sql = substr_replace($sql,  '(' . $sign . ')', $start_pos,$length);
			return [$sql, $start_pos+1, $length, $sign];
		}
		for($i=0;($token = $sql{$start_pos + $length + $i})!==')';$i++){}
		if($token !== ')'){
			$plus++;
			$sql = substr_replace($sql,  '(' . $sign . ')', $start_pos,$length);
			return [$sql, $start_pos+1, $length, $sign];
		}
		return [$sql,$start_pos];
	}

    /**
     * Slice the SQL statement around pairs of quotes and
     * return string fragments of SQL outside of quoted literals.
     * Each fragment is captured as a 2-element array:
     *
     * 0 => matched fragment string,
     * 1 => offset of fragment in $statement
     *
     * @param string $statement
     * @return array
     */
    static private function getUnquotedStatementFragments($statement){
        $literal = self::ESCAPED_SINGLE_QUOTED_TEXT . '|' .
                   self::ESCAPED_DOUBLE_QUOTED_TEXT . '|' .
                   self::ESCAPED_BACKTICK_QUOTED_TEXT . '|' .
                   self::ESCAPED_BRACKET_QUOTED_TEXT;
        preg_match_all("/([^'\"`\[]+)(?:$literal)?/s", $statement, $fragments, PREG_OFFSET_CAPTURE);

        return $fragments[1];
    }
}
