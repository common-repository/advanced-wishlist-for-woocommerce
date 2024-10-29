<?php

namespace AlgolWishlist;

class WordpressRewrite
{
    /**
     * @var array<int, IRewriteRule>
     */
    protected $rules;

    public function __construct()
    {
        $this->rules = [];
    }

    public function addRewriteRule(IRewriteRule $rule)
    {
        $this->rules[] = $rule;
    }

    public function register()
    {
        add_action("init", [$this, "registerRewriteRule"]);
        add_filter("query_vars", [$this, 'addQueryVar']);
    }

    public function addQueryVar($queryVars)
    {
        foreach ( $this->rules as $rule ) {
            $queryVars[] = $rule->getQueryParam();
        }

        return $queryVars;
    }

    public function registerRewriteRule()
    {
        $patterns = [];

        foreach ( $this->rules as $rule ) {
            if ( $pattern = $rule->register() ) {
                $patterns[] = $pattern;
            };
        }

        $rewriteRules = get_option('rewrite_rules');
        if (!is_array($rewriteRules) || array_diff_key($patterns, $rewriteRules)) {
            flush_rewrite_rules();
        }
    }
}
