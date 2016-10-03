<?php

namespace Ddmaster\PostgreSearchBundle\DQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;

/**
 * TsqueryFunction ::= "PLAINTO_TSQUERY" "(" StringPrimary "," StringPrimary "," StringPrimary ")".
 *
 * @see https://github.com/1on/postgres-search-bundle
 */
class PlainToTsqueryFunction extends FunctionNode
{
    public $fieldName = null;
    public $queryString = null;
    public $regconfig = null;

    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->fieldName = $parser->StringPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->queryString = $parser->StringPrimary();

        if ($parser->getLexer()->lookahead['type'] == Lexer::T_COMMA) {
            $parser->match(Lexer::T_COMMA);
            $this->regconfig = $parser->StringPrimary();
        }

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        if (!is_null($this->regconfig)) {
            $result =
                $this->fieldName->dispatch($sqlWalker)
                .' @@ plainto_tsquery('
                .$this->regconfig->dispatch($sqlWalker).', '
                .$this->queryString->dispatch($sqlWalker).')';
        } else {
            $result = $this->fieldName->dispatch($sqlWalker)
                .' @@ plainto_tsquery('.$this->queryString->dispatch($sqlWalker).')';
        }

        return $result;
    }
}
