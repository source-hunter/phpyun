<?php

class Smarty_Compiler extends Smarty
{
    var $_folded_blocks         =   array();    // keeps folded template blocks
    var $_current_file          =   null;       // the current template being compiled
    var $_current_line_no       =   1;          // line number for error messages
    var $_capture_stack         =   array();    // keeps track of nested capture buffers
    var $_plugin_info           =   array();    // keeps track of plugins to load
    var $_init_smarty_vars      =   false;
    var $_permitted_tokens      =   array('true','false','yes','no','on','off','null');
    var $_db_qstr_regexp        =   null;        // regexps are setup in the constructor
    var $_si_qstr_regexp        =   null;
    var $_qstr_regexp           =   null;
    var $_func_regexp           =   null;
    var $_reg_obj_regexp        =   null;
    var $_var_bracket_regexp    =   null;
    var $_num_const_regexp      =   null;
    var $_dvar_guts_regexp      =   null;
    var $_dvar_regexp           =   null;
    var $_cvar_regexp           =   null;
    var $_svar_regexp           =   null;
    var $_avar_regexp           =   null;
    var $_mod_regexp            =   null;
    var $_var_regexp            =   null;
    var $_parenth_param_regexp  =   null;
    var $_func_call_regexp      =   null;
    var $_obj_ext_regexp        =   null;
    var $_obj_start_regexp      =   null;
    var $_obj_params_regexp     =   null;
    var $_obj_call_regexp       =   null;
    var $_cacheable_state       =   0;
    var $_cache_attrs_count     =   0;
    var $_nocache_count         =   0;
    var $_cache_serial          =   null;
    var $_cache_include         =   null;
    var $_strip_depth           =   0;
    var $_additional_newline    =   "\n";
	var $company_statis			=	'';
	var $company_rating			=	'';
	var $news_group				=	'';
    /**#@-*/
    /**
     * The class constructor.
     */
    function Smarty_Compiler()
    {
        $this->_db_qstr_regexp = '"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"';
        $this->_si_qstr_regexp = '\'[^\'\\\\]*(?:\\\\.[^\'\\\\]*)*\'';
        // matches single or double quoted strings
        $this->_qstr_regexp = '(?:' . $this->_db_qstr_regexp . '|' . $this->_si_qstr_regexp . ')';
        $this->_var_bracket_regexp = '\[\$?[\w\.]+\]';
        $this->_num_const_regexp = '(?:\-?\d+(?:\.\d+)?)';
        $this->_dvar_math_regexp = '(?:[\+\*\/\%]|(?:-(?!>)))';
        $this->_dvar_math_var_regexp = '[\$\w\.\+\-\*\/\%\d\>\[\]]';
        $this->_dvar_guts_regexp = '\w+(?:' . $this->_var_bracket_regexp
                . ')*(?:\.\$?\w+(?:' . $this->_var_bracket_regexp . ')*)*(?:' . $this->_dvar_math_regexp . '(?:' . $this->_num_const_regexp . '|' . $this->_dvar_math_var_regexp . ')*)?';
        $this->_dvar_regexp = '\$' . $this->_dvar_guts_regexp;
        // matches config vars:
        // #foo#
        // #foobar123_foo#
        $this->_cvar_regexp = '\#\w+\#';
        // matches section vars:
        // %foo.bar%
        $this->_svar_regexp = '\%\w+\.\w+\%';
        // matches all valid variables (no quotes, no modifiers)
        $this->_avar_regexp = '(?:' . $this->_dvar_regexp . '|'
           . $this->_cvar_regexp . '|' . $this->_svar_regexp . ')';
        $this->_var_regexp = '(?:' . $this->_avar_regexp . '|' . $this->_qstr_regexp . ')';
        $this->_obj_ext_regexp = '\->(?:\$?' . $this->_dvar_guts_regexp . ')';
        $this->_obj_restricted_param_regexp = '(?:'
                . '(?:' . $this->_var_regexp . '|' . $this->_num_const_regexp . ')(?:' . $this->_obj_ext_regexp . '(?:\((?:(?:' . $this->_var_regexp . '|' . $this->_num_const_regexp . ')'
                . '(?:\s*,\s*(?:' . $this->_var_regexp . '|' . $this->_num_const_regexp . '))*)?\))?)*)';
        $this->_obj_single_param_regexp = '(?:\w+|' . $this->_obj_restricted_param_regexp . '(?:\s*,\s*(?:(?:\w+|'
                . $this->_var_regexp . $this->_obj_restricted_param_regexp . ')))*)';
        $this->_obj_params_regexp = '\((?:' . $this->_obj_single_param_regexp
                . '(?:\s*,\s*' . $this->_obj_single_param_regexp . ')*)?\)';
        $this->_obj_start_regexp = '(?:' . $this->_dvar_regexp . '(?:' . $this->_obj_ext_regexp . ')+)';
        $this->_obj_call_regexp = '(?:' . $this->_obj_start_regexp . '(?:' . $this->_obj_params_regexp . ')?(?:' . $this->_dvar_math_regexp . '(?:' . $this->_num_const_regexp . '|' . $this->_dvar_math_var_regexp . ')*)?)';
        $this->_mod_regexp = '(?:\|@?\w+(?::(?:\w+|' . $this->_num_const_regexp . '|'
           . $this->_obj_call_regexp . '|' . $this->_avar_regexp . '|' . $this->_qstr_regexp .'))*)';
        $this->_func_regexp = '[a-zA-Z_]\w*';
        $this->_reg_obj_regexp = '[a-zA-Z_]\w*->[a-zA-Z_]\w*';
        $this->_param_regexp = '(?:\s*(?:' . $this->_obj_call_regexp . '|'
           . $this->_var_regexp . '|' . $this->_num_const_regexp  . '|\w+)(?>' . $this->_mod_regexp . '*)\s*)';
        $this->_parenth_param_regexp = '(?:\((?:\w+|'
                . $this->_param_regexp . '(?:\s*,\s*(?:(?:\w+|'
                . $this->_param_regexp . ')))*)?\))';
        $this->_func_call_regexp = '(?:' . $this->_func_regexp . '\s*(?:'
           . $this->_parenth_param_regexp . '))';

    }
    /**
     * compile a resource
     *
     * sets $compiled_content to the compiled source
     * @param string $resource_name
     * @param string $source_content
     * @param string $compiled_content
     * @return true
     */
    function _compile_file($resource_name, $source_content, &$compiled_content)
    {

        if ($this->security)
		{
            // do not allow php syntax to be executed unless specified
            if ($this->php_handling == SMARTY_PHP_ALLOW &&
                !$this->security_settings['PHP_HANDLING']) {
                $this->php_handling = SMARTY_PHP_PASSTHRU;
            }
        }
        $this->_load_filters();
        $this->_current_file = $resource_name;
        $this->_current_line_no = 1;
        $ldq = preg_quote($this->left_delimiter, '~');
        $rdq = preg_quote($this->right_delimiter, '~');
        // run template source through prefilter functions
        if (count($this->_plugins['prefilter']) > 0)
		{
            foreach ($this->_plugins['prefilter'] as $filter_name => $prefilter)
			{
                if ($prefilter === false) continue;
                if ($prefilter[3] || is_callable($prefilter[0]))
				{
                    $source_content = call_user_func_array($prefilter[0],
                                                            array($source_content, &$this));
                    $this->_plugins['prefilter'][$filter_name][3] = true;
                } else {
                    $this->_trigger_fatal_error("[plugin] prefilter '$filter_name' is not implemented");
                }
            }
        }
        /* fetch all special blocks */
        $search = "~{$ldq}\*(.*?)\*{$rdq}|{$ldq}\s*literal\s*{$rdq}(.*?){$ldq}\s*/literal\s*{$rdq}|{$ldq}\s*php\s*{$rdq}(.*?){$ldq}\s*/php\s*{$rdq}~s";
        preg_match_all($search, $source_content, $match,  PREG_SET_ORDER);
        $this->_folded_blocks = $match;
        reset($this->_folded_blocks);
        /* replace special blocks by "{php}" */
        $source_content = preg_replace($search.'e', "'"
                                       . $this->_quote_replace($this->left_delimiter) . 'php'
                                       . "' . str_repeat(\"\n\", substr_count('\\0', \"\n\")) .'"
                                       . $this->_quote_replace($this->right_delimiter)
                                       . "'"
                                       , $source_content);

        /* Gather all template tags. */
        preg_match_all("~{$ldq}\s*(.*?)\s*{$rdq}~s", $source_content, $_match);
        $template_tags = $_match[1];
        /* Split content by template tags to obtain non-template content. */
        $text_blocks = preg_split("~{$ldq}.*?{$rdq}~s", $source_content);
        /* loop through text blocks */
        for ($curr_tb = 0, $for_max = count($text_blocks); $curr_tb < $for_max; $curr_tb++) {
            /* match anything resembling php tags */
            if (preg_match_all('~(<\?(?:\w+|=)?|\?>|language\s*=\s*[\"\']?\s*php\s*[\"\']?)~is', $text_blocks[$curr_tb], $sp_match)) {
                /* replace tags with placeholders to prevent recursive replacements */
                $sp_match[1] = array_unique($sp_match[1]);
                usort($sp_match[1], '_smarty_sort_length');
                for ($curr_sp = 0, $for_max2 = count($sp_match[1]); $curr_sp < $for_max2; $curr_sp++) {
                    $text_blocks[$curr_tb] = str_replace($sp_match[1][$curr_sp],'%%%SMARTYSP'.$curr_sp.'%%%',$text_blocks[$curr_tb]);
                }
                /* process each one */
                for ($curr_sp = 0, $for_max2 = count($sp_match[1]); $curr_sp < $for_max2; $curr_sp++) {
                    if ($this->php_handling == SMARTY_PHP_PASSTHRU) {
                        /* echo php contents */
                        $text_blocks[$curr_tb] = str_replace('%%%SMARTYSP'.$curr_sp.'%%%', '<?php echo \''.str_replace("'", "\'", $sp_match[1][$curr_sp]).'\'; ?>'."\n", $text_blocks[$curr_tb]);
                    } else if ($this->php_handling == SMARTY_PHP_QUOTE) {
                        /* quote php tags */
                        $text_blocks[$curr_tb] = str_replace('%%%SMARTYSP'.$curr_sp.'%%%', htmlspecialchars($sp_match[1][$curr_sp]), $text_blocks[$curr_tb]);
                    } else if ($this->php_handling == SMARTY_PHP_REMOVE) {
                        /* remove php tags */
                        $text_blocks[$curr_tb] = str_replace('%%%SMARTYSP'.$curr_sp.'%%%', '', $text_blocks[$curr_tb]);
                    } else {
                        /* SMARTY_PHP_ALLOW, but echo non php starting tags */
                        $sp_match[1][$curr_sp] = preg_replace('~(<\?(?!php|=|$))~i', '<?php echo \'\\1\'?>'."\n", $sp_match[1][$curr_sp]);
                        $text_blocks[$curr_tb] = str_replace('%%%SMARTYSP'.$curr_sp.'%%%', $sp_match[1][$curr_sp], $text_blocks[$curr_tb]);
                    }
                }
            }
        }
        /* Compile the template tags into PHP code. */
        $compiled_tags = array();
        for ($i = 0, $for_max = count($template_tags); $i < $for_max; $i++)
		{
            $this->_current_line_no += substr_count($text_blocks[$i], "\n");
            $compiled_tags[] = $this->_compile_tag($template_tags[$i]);
            $this->_current_line_no += substr_count($template_tags[$i], "\n");
        }
        if (count($this->_tag_stack)>0)
		{
            list($_open_tag, $_line_no) = end($this->_tag_stack);
            $this->_syntax_error("unclosed tag \{$_open_tag} (opened line $_line_no).", E_USER_ERROR, __FILE__, __LINE__);
            return;
        }
        /* Reformat $text_blocks between 'strip' and '/strip' tags,
           removing spaces, tabs and newlines. */
        $strip = false;
        for ($i = 0, $for_max = count($compiled_tags); $i < $for_max; $i++)
		{
            if ($compiled_tags[$i] == '{strip}')
			{
                $compiled_tags[$i] = '';
                $strip = true;
                /* remove leading whitespaces */
                $text_blocks[$i + 1] = ltrim($text_blocks[$i + 1]);
            }
            if ($strip)
			{
                /* strip all $text_blocks before the next '/strip' */
                for ($j = $i + 1; $j < $for_max; $j++)
				{
                    /* remove leading and trailing whitespaces of each line */
                    $text_blocks[$j] = preg_replace('![\t ]*[\r\n]+[\t ]*!', '', $text_blocks[$j]);
                    if ($compiled_tags[$j] == '{/strip}')
					{
                        /* remove trailing whitespaces from the last text_block */
                        $text_blocks[$j] = rtrim($text_blocks[$j]);
                    }
                    $text_blocks[$j] = "<?php echo '" . strtr($text_blocks[$j], array("'"=>"\'", "\\"=>"\\\\")) . "'; ?>";
                    if ($compiled_tags[$j] == '{/strip}')
					{
                        $compiled_tags[$j] = "\n"; /* slurped by php, but necessary
                                    if a newline is following the closing strip-tag */
                        $strip = false;
                        $i = $j;
                        break;
                    }
                }
            }
        }
        $compiled_content = '';
        $tag_guard = '%%%SMARTYOTG' . md5(uniqid(rand(), true)) . '%%%';
        /* Interleave the compiled contents and text blocks to get the final result. */
        for ($i = 0, $for_max = count($compiled_tags); $i < $for_max; $i++)
		{
            if ($compiled_tags[$i] == '')
			{
                // tag result empty, remove first newline from following text block
                $text_blocks[$i+1] = preg_replace('~^(\r\n|\r|\n)~', '', $text_blocks[$i+1]);
            }
            // replace legit PHP tags with placeholder
            $text_blocks[$i] = str_replace('<?', $tag_guard, $text_blocks[$i]);
            $compiled_tags[$i] = str_replace('<?', $tag_guard, $compiled_tags[$i]);
            $compiled_content .= $text_blocks[$i] . $compiled_tags[$i];
        }
        $compiled_content .= str_replace('<?', $tag_guard, $text_blocks[$i]);
        // escape php tags created by interleaving
        $compiled_content = str_replace('<?', "<?php echo '<?' ?>\n", $compiled_content);
        $compiled_content = preg_replace("~(?<!')language\s*=\s*[\"\']?\s*php\s*[\"\']?~", "<?php echo 'language=php' ?>\n", $compiled_content);
        // recover legit tags
        $compiled_content = str_replace($tag_guard, '<?', $compiled_content);
        // remove \n from the end of the file, if any
        if (strlen($compiled_content) && (substr($compiled_content, -1) == "\n") )
		{
            $compiled_content = substr($compiled_content, 0, -1);
        }
        if (!empty($this->_cache_serial))
		{
            $compiled_content = "<?php \$this->_cache_serials['".$this->_cache_include."'] = '".$this->_cache_serial."'; ?>" . $compiled_content;
        }
        // run compiled template through postfilter functions
        if (count($this->_plugins['postfilter']) > 0)
		{
            foreach ($this->_plugins['postfilter'] as $filter_name => $postfilter)
			{
                if ($postfilter === false) continue;
                if ($postfilter[3] || is_callable($postfilter[0]))
				{
                    $compiled_content = call_user_func_array($postfilter[0],
                                                              array($compiled_content, &$this));
                    $this->_plugins['postfilter'][$filter_name][3] = true;
                } else {
                    $this->_trigger_fatal_error("Smarty plugin error: postfilter '$filter_name' is not implemented");
                }
            }
        }
        // put header at the top of the compiled template
        $template_header = "<?php /* Smarty version ".$this->_version.", created on ".strftime("%Y-%m-%d %H:%M:%S")."\n";
        $template_header .= "         compiled from ".strtr(urlencode($resource_name), array('%2F'=>'/', '%3A'=>':'))." */ ?>\n";
        /* Emit code to load needed plugins. */
        $this->_plugins_code = '';
        if (count($this->_plugin_info))
		{
            $_plugins_params = "array('plugins' => array(";
            foreach ($this->_plugin_info as $plugin_type => $plugins)
			{
                foreach ($plugins as $plugin_name => $plugin_info)
				{
                    $_plugins_params .= "array('$plugin_type', '$plugin_name', '" . strtr($plugin_info[0], array("'" => "\\'", "\\" => "\\\\")) . "', $plugin_info[1], ";
                    $_plugins_params .= $plugin_info[2] ? 'true),' : 'false),';
                }
            }
            $_plugins_params .= '))';
            $plugins_code = "<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');\nsmarty_core_load_plugins($_plugins_params, \$this); ?>\n";
            $template_header .= $plugins_code;
            $this->_plugin_info = array();
            $this->_plugins_code = $plugins_code;
        }
        if ($this->_init_smarty_vars)
		{
            $template_header .= "<?php require_once(SMARTY_CORE_DIR . 'core.assign_smarty_interface.php');\nsmarty_core_assign_smarty_interface(null, \$this); ?>\n";
            $this->_init_smarty_vars = false;
        }
        $compiled_content = $template_header . $compiled_content;
        return true;
    }
    /**
     * Compile a template tag
     *
     * @param string $template_tag
     * @return string
     */
    function _compile_tag($template_tag)
    {

        /* Matched comment. */
        if (substr($template_tag, 0, 1) == '*' && substr($template_tag, -1) == '*')
            return '';

        /* Split tag into two three parts: command, command modifiers and the arguments. */
        if(! preg_match('~^(?:(' . $this->_num_const_regexp . '|' . $this->_obj_call_regexp . '|' . $this->_var_regexp
                . '|\/?' . $this->_reg_obj_regexp . '|\/?' . $this->_func_regexp . ')(' . $this->_mod_regexp . '*))
                      (?:\s+(.*))?$
                    ~xs', $template_tag, $match)) {
            $this->_syntax_error("unrecognized tag: $template_tag", E_USER_ERROR, __FILE__, __LINE__);
        }
        $tag_command = $match[1];
        $tag_modifier = isset($match[2]) ? $match[2] : null;
        $tag_args = isset($match[3]) ? $match[3] : null;
        if (preg_match('~^' . $this->_num_const_regexp . '|' . $this->_obj_call_regexp . '|' . $this->_var_regexp . '$~', $tag_command)) {
            /* tag name is a variable or object */
            $_return = $this->_parse_var_props($tag_command . $tag_modifier);
            return "<?php echo $_return; ?>" . $this->_additional_newline;
        }
        /* If the tag name is a registered object, we process it. */
        if (preg_match('~^\/?' . $this->_reg_obj_regexp . '$~', $tag_command)) {
            return $this->_compile_registered_object_tag($tag_command, $this->_parse_attrs($tag_args), $tag_modifier);
        }

        switch ($tag_command)
		{
            case 'include':
                return $this->_compile_include_tag($tag_args);
            case 'include_php':
                return $this->_compile_include_php_tag($tag_args);
            case 'if':
                $this->_push_tag('if');
                return $this->_compile_if_tag($tag_args);
            case 'else':
                list($_open_tag) = end($this->_tag_stack);
                if ($_open_tag != 'if' && $_open_tag != 'elseif')
                    $this->_syntax_error('unexpected {else}', E_USER_ERROR, __FILE__, __LINE__);
                else
                    $this->_push_tag('else');
                return '<?php else: ?>';
            case 'elseif':
                list($_open_tag) = end($this->_tag_stack);
                if ($_open_tag != 'if' && $_open_tag != 'elseif')
                    $this->_syntax_error('unexpected {elseif}', E_USER_ERROR, __FILE__, __LINE__);
                if ($_open_tag == 'if')
                    $this->_push_tag('elseif');
                return $this->_compile_if_tag($tag_args, true);
            case '/if':
                $this->_pop_tag('if');
                return '<?php endif; ?>';
            case 'capture':
                return $this->_compile_capture_tag(true, $tag_args);
            case '/capture':
                return $this->_compile_capture_tag(false);
            case 'ldelim':
                return $this->left_delimiter;
            case 'rdelim':
                return $this->right_delimiter;
            case 'section':
                $this->_push_tag('section');
                return $this->_compile_section_start($tag_args);
            case 'sectionelse':
                $this->_push_tag('sectionelse');
                return "<?php endfor; else: ?>";
                break;
            case '/section':
                $_open_tag = $this->_pop_tag('section');
                if ($_open_tag == 'sectionelse')
                    return "<?php endif; ?>";
                else
                    return "<?php endfor; endif; ?>";
            case 'foreach':
                $this->_push_tag('foreach');
                return $this->_compile_foreach_start($tag_args);
                break;
            case 'foreachelse':
                $this->_push_tag('foreachelse');
                return "<?php endforeach; else: ?>";
            case '/foreach':
                $_open_tag = $this->_pop_tag('foreach');
                if ($_open_tag == 'foreachelse')
                    return "<?php endif; unset(\$_from); ?>";
                else
                    return "<?php endforeach; endif; unset(\$_from); ?>";
                break;
			  
			case 'nav':
			 	$this->_push_tag('nav');
				return $this->_complie_nav_start($tag_args);
				break;
			case '/nav':
				$this->_pop_tag('nav');
				return "<?php endforeach; endif; unset(\$_from); ?>";
				break;
			 
			case 'navmap':
			 	$this->_push_tag('navmap');
				return $this->_complie_navmap_start($tag_args);
				break;
			case '/navmap':
				$this->_pop_tag('navmap');
				return "<?php endforeach; endif; unset(\$_from); ?>";
				break;
			case 'articleclass':
			 	$this->_push_tag('articleclass');
				return $this->_complie_articleclass_start($tag_args);
				break;
			case '/articleclass':
				$this->_pop_tag('articleclass');
				return "<?php endforeach; endif; unset(\$_from); ?>";
				break;
			
			case 'joblist':
			 	$this->_push_tag('joblist');
				return $this->_complie_joblist_start($tag_args);
				break;
			case '/joblist':
				$this->_pop_tag('joblist');
				return "<?php endforeach; endif; unset(\$_from); ?>";
				break;
			
			case 'look':
			 	$this->_push_tag('look');
				return $this->_complie_look_start($tag_args);
				break;
			case '/look':
				$this->_pop_tag('look');
				return "<?php endforeach; endif; unset(\$_from); ?>";
				break;
			case 'looksq':
			 	$this->_push_tag('looksq');
				return $this->_complie_looksq_start($tag_args);
				break;
			case '/looksq':
				$this->_pop_tag('looksq');
				return "<?php endforeach; endif; unset(\$_from); ?>";
				break;
			
			case 'userlist':
			 	$this->_push_tag('userlist');
				return $this->_complie_userlist_start($tag_args);
				break;
			case '/userlist':
				$this->_pop_tag('userlist');
				return "<?php endforeach; endif; unset(\$_from); ?>";
				break;
			case 'announcement':
			 	$this->_push_tag('announcement');
				return $this->_complie_announcement_start($tag_args);
				break;
			case '/announcement':
				$this->_pop_tag('announcement');
				return "<?php endforeach; endif; unset(\$_from); ?>";
				break;
			
			case 'downlist':
			 	$this->_push_tag('downlist');
				return $this->_complie_downlist_start($tag_args);
				break;
			case '/downlist':
				$this->_pop_tag('downlist');
				return "<?php endforeach; endif; unset(\$_from); ?>";
				break;
			
			case 'link':
			 	$this->_push_tag('link');
				return $this->_complie_link_start($tag_args);
				break;
			case '/link':
				$this->_pop_tag('link');
				return "<?php endforeach; endif; unset(\$_from); ?>";
				break;
			
			case 'msglist':
			 	$this->_push_tag('msglist');
				return $this->_complie_msglist_start($tag_args);
				break;
			case '/msglist':
				$this->_pop_tag('msglist');
				return "<?php endforeach; endif; unset(\$_from); ?>";
				break;
			
			case 'adlist':
			 	$this->_push_tag('adlist');
				return $this->_complie_adlist_start($tag_args);
				break;
			case '/adlist':
				$this->_pop_tag('adlist');
				return "<?php endforeach; endif; unset(\$_from); ?>";
				break;

			
			case 'key':
			 	$this->_push_tag('key');
				return $this->_complie_key_start($tag_args);
				break;
			case '/key':
				$this->_pop_tag('key');
				return "<?php endforeach; endif; unset(\$_from); ?>";
				break;
			
			case 'article':
			 	$this->_push_tag('article');
				return $this->_complie_article_start($tag_args);
				break;
			case '/article':
				$this->_pop_tag('article');
				return "<?php endforeach; endif; unset(\$_from); ?>";
				break;
			
			case 'comjob':
			 	$this->_push_tag('comjob');
				return $this->_complie_comjob_start($tag_args);
				break;
			case '/comjob':
				$this->_pop_tag('comjob');
				return "<?php endforeach; endif; unset(\$_from); ?>";
				break;
			
			case 'comlist':
			 	$this->_push_tag('comlist');
				return $this->_complie_comlist_start($tag_args);
				break;
			case '/comlist':
				$this->_pop_tag('comlist');
				return "<?php endforeach; endif; unset(\$_from); ?>";
				break;
			
			case 'maplist':
			 	$this->_push_tag('maplist');
				return $this->_complie_maplist_start($tag_args);
				break;
			case '/maplist':
				$this->_pop_tag('maplist');
				return "<?php endforeach; endif; unset(\$_from); ?>";
				break;
			
			case 'singlenav':
			 	$this->_push_tag('singlenav');
				return $this->_complie_singlenav_start($tag_args);
				break;
			case '/singlenav':
				$this->_pop_tag('singlenav');
				return "<?php endforeach; endif; unset(\$_from); ?>";
				break;
			
			case 'fast':
			 	$this->_push_tag('fast');
				return $this->_complie_fast_start($tag_args);
				break;
			case '/fast':
				$this->_pop_tag('fast');
				return "<?php endforeach; endif; unset(\$_from); ?>";
				break;
			
			case 'tiny':
			 	$this->_push_tag('tiny');
				return $this->_complie_tiny_start($tag_args);
				break;
			case '/tiny':
				$this->_pop_tag('tiny');
				return "<?php endforeach; endif; unset(\$_from); ?>";
				break;
			
			case 'fairs':
			 	$this->_push_tag('fairs');
				return $this->_complie_fairs_start($tag_args);
				break;
			case '/fairs':
				$this->_pop_tag('fairs');
				return "<?php endforeach; endif; unset(\$_from); ?>";
				break;
			
			case 'hotjob':
			 	$this->_push_tag('hotjob');
				return $this->_complie_hotjob_start($tag_args);
				break;
			case '/hotjob':
				$this->_pop_tag('hotjob');
				return "<?php endforeach; endif; unset(\$_from); ?>";
				break;
			
			case 'hrclass':
			 	$this->_push_tag('hrclass');
				return $this->_complie_hrclass_start($tag_args);
				break;
			case '/hrclass':
				$this->_pop_tag('hrclass');
				return "<?php endforeach; endif; unset(\$_from); ?>";
				break;
			
			case 'hrlist':
			 	$this->_push_tag('hrlist');
				return $this->_complie_hrlist_start($tag_args);
				break;
			case '/hrlist':
				$this->_pop_tag('hrlist');
				return "<?php endforeach; endif; unset(\$_from); ?>";
				break;
            case 'strip':
            case '/strip':
                if (substr($tag_command, 0, 1)=='/')
				{
                    $this->_pop_tag('strip');
                    if (--$this->_strip_depth==0)
					{ 
                        $this->_additional_newline = "\n";
                        return '{' . $tag_command . '}';
                    }
                } else {
                    $this->_push_tag('strip');
                    if ($this->_strip_depth++==0) {
                        $this->_additional_newline = "";
                        return '{' . $tag_command . '}';
                    }
                }
                return '';
            case 'php':
              
                list(, $block) = each($this->_folded_blocks);
                $this->_current_line_no += substr_count($block[0], "\n");
                /* the number of matched elements in the regexp in _compile_file()
                   determins the type of folded tag that was found */
                switch (count($block))
				{
                    case 2: /* comment */
                        return '';

                    case 3: /* literal */
                        return "<?php echo '" . strtr($block[2], array("'"=>"\'", "\\"=>"\\\\")) . "'; ?>" . $this->_additional_newline;

                    case 4: /* php */
                        if ($this->security && !$this->security_settings['PHP_TAGS']) {
                            $this->_syntax_error("(secure mode) php tags not permitted", E_USER_WARNING, __FILE__, __LINE__);
                            return;
                        }
                        return '<?php ' . $block[3] .' ?>';
                }
                break;
            case 'insert':
                return $this->_compile_insert_tag($tag_args);

			
			case 'qlist':
			 	$this->_push_tag('qlist');
				return $this->_complie_qlist_start($tag_args);
				break;
			case '/qlist':
				$this->_pop_tag('qlist');
				return "<?php endforeach; endif; unset(\$_from); ?>";
				break;
			
			case 'mlist':
			 	$this->_push_tag('mlist');
				return $this->_complie_mlist_start($tag_args);
				break;
			case '/mlist':
				$this->_pop_tag('mlist');
				return "<?php endforeach; endif; unset(\$_from); ?>";
				break;
			
			case 'qrecom':
			 	$this->_push_tag('qrecom');
				return $this->_complie_qrecom_start($tag_args);
				break;
			case '/qrecom':
				$this->_pop_tag('qrecom');
				return "<?php endforeach; endif; unset(\$_from); ?>";
				break;
            default:
                if ($this->_compile_compiler_tag($tag_command, $tag_args, $output)) {
                    return $output;
                } else if ($this->_compile_block_tag($tag_command, $tag_args, $tag_modifier, $output)) {
                    return $output;
                } else if ($this->_compile_custom_tag($tag_command, $tag_args, $tag_modifier, $output)) {
                    return $output;
                } else {
                    $this->_syntax_error("unrecognized tag '$tag_command'", E_USER_ERROR, __FILE__, __LINE__);
                }
        }

    }
    /**
     * compile the custom compiler tag
     *
     * sets $output to the compiled custom compiler tag
     * @param string $tag_command
     * @param string $tag_args
     * @param string $output
     * @return boolean
     */
    function _compile_compiler_tag($tag_command, $tag_args, &$output)
    {
        $found = false;
        $have_function = true;
        /*
         * First we check if the compiler function has already been registered
         * or loaded from a plugin file.
         */
        if (isset($this->_plugins['compiler'][$tag_command]))
		{
            $found = true;
            $plugin_func = $this->_plugins['compiler'][$tag_command][0];
            if (!is_callable($plugin_func)) {
                $message = "compiler function '$tag_command' is not implemented";
                $have_function = false;
            }
        }
        /*
         * Otherwise we need to load plugin file and look for the function
         * inside it.
         */
        else if ($plugin_file = $this->_get_plugin_filepath('compiler', $tag_command))
		{
            $found = true;
            include_once $plugin_file;
            $plugin_func = 'smarty_compiler_' . $tag_command;
            if (!is_callable($plugin_func))
			{
                $message = "plugin function $plugin_func() not found in $plugin_file\n";
                $have_function = false;
            } else {
                $this->_plugins['compiler'][$tag_command] = array($plugin_func, null, null, null, true);
            }
        }
        /*
         * True return value means that we either found a plugin or a
         * dynamically registered function. False means that we didn't and the
         * compiler should now emit code to load custom function plugin for this
         * tag.
         */
        if ($found)
		{
            if ($have_function)
			{
                $output = call_user_func_array($plugin_func, array($tag_args, &$this));
                if($output != '')
				{
                $output = '<?php ' . $this->_push_cacheable_state('compiler', $tag_command)
                                   . $output
                                   . $this->_pop_cacheable_state('compiler', $tag_command) . ' ?>';
                }
            } else {
                $this->_syntax_error($message, E_USER_WARNING, __FILE__, __LINE__);
            }
            return true;
        } else {
            return false;
        }
    }
    /**
     * compile block function tag
     *
     * sets $output to compiled block function tag
     * @param string $tag_command
     * @param string $tag_args
     * @param string $tag_modifier
     * @param string $output
     * @return boolean
     */
    function _compile_block_tag($tag_command, $tag_args, $tag_modifier, &$output)
    {
        if (substr($tag_command, 0, 1) == '/')
		{
            $start_tag = false;
            $tag_command = substr($tag_command, 1);
        } else
            $start_tag = true;
        $found = false;
        $have_function = true;
        /*
         * First we check if the block function has already been registered
         * or loaded from a plugin file.
         */
        if (isset($this->_plugins['block'][$tag_command]))
		{
            $found = true;
            $plugin_func = $this->_plugins['block'][$tag_command][0];
            if (!is_callable($plugin_func))
			{
                $message = "block function '$tag_command' is not implemented";
                $have_function = false;
            }
        }
        /*
         * Otherwise we need to load plugin file and look for the function
         * inside it.
         */
        else if ($plugin_file = $this->_get_plugin_filepath('block', $tag_command)) {
            $found = true;
            include_once $plugin_file;
            $plugin_func = 'smarty_block_' . $tag_command;
            if (!function_exists($plugin_func)) {
                $message = "plugin function $plugin_func() not found in $plugin_file\n";
                $have_function = false;
            } else {
                $this->_plugins['block'][$tag_command] = array($plugin_func, null, null, null, true);

            }
        }
        if (!$found)
		{
            return false;
        } else if (!$have_function)
		{
            $this->_syntax_error($message, E_USER_WARNING, __FILE__, __LINE__);
            return true;
        }
        /*
         * Even though we've located the plugin function, compilation
         * happens only once, so the plugin will still need to be loaded
         * at runtime for future requests.
         */
        $this->_add_plugin('block', $tag_command);
        if ($start_tag)
            $this->_push_tag($tag_command);
        else
            $this->_pop_tag($tag_command);

        if ($start_tag)
		{
            $output = '<?php ' . $this->_push_cacheable_state('block', $tag_command);
            $attrs = $this->_parse_attrs($tag_args);
            $_cache_attrs='';
            $arg_list = $this->_compile_arg_list('block', $tag_command, $attrs, $_cache_attrs);
            $output .= "$_cache_attrs\$this->_tag_stack[] = array('$tag_command', array(".@implode(',', $arg_list).')); ';
            $output .= '$_block_repeat=true;' . $this->_compile_plugin_call('block', $tag_command).'($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);';
            $output .= 'while ($_block_repeat) { ob_start(); ?>';
        } else {
            $output = '<?php $_block_content = ob_get_contents(); ob_end_clean(); ';
            $_out_tag_text = $this->_compile_plugin_call('block', $tag_command).'($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat)';
            if ($tag_modifier != '') {
                $this->_parse_modifiers($_out_tag_text, $tag_modifier);
            }
            $output .= '$_block_repeat=false;echo ' . $_out_tag_text . '; } ';
            $output .= " array_pop(\$this->_tag_stack); " . $this->_pop_cacheable_state('block', $tag_command) . '?>';
        }

        return true;
    }
    /**
     * compile custom function tag
     *
     * @param string $tag_command
     * @param string $tag_args
     * @param string $tag_modifier
     * @return string
     */
    function _compile_custom_tag($tag_command, $tag_args, $tag_modifier, &$output)
    {
        $found = false;
        $have_function = true;
        /*
         * First we check if the custom function has already been registered
         * or loaded from a plugin file.
         */
        if (isset($this->_plugins['function'][$tag_command]))
		{
            $found = true;
            $plugin_func = $this->_plugins['function'][$tag_command][0];
            if (!is_callable($plugin_func)) {
                $message = "custom function '$tag_command' is not implemented";
                $have_function = false;
            }
        }
        /*
         * Otherwise we need to load plugin file and look for the function
         * inside it.
         */
        else if ($plugin_file = $this->_get_plugin_filepath('function', $tag_command))
		{
            $found = true;
            include_once $plugin_file;
            $plugin_func = 'smarty_function_' . $tag_command;
            if (!function_exists($plugin_func))
			{
                $message = "plugin function $plugin_func() not found in $plugin_file\n";
                $have_function = false;
            } else {
                $this->_plugins['function'][$tag_command] = array($plugin_func, null, null, null, true);

            }
        }
        if (!$found)
		{
            return false;
        } else if (!$have_function)
		{
            $this->_syntax_error($message, E_USER_WARNING, __FILE__, __LINE__);
            return true;
        }
        /* declare plugin to be loaded on display of the template that
           we compile right now */
        $this->_add_plugin('function', $tag_command);
        $_cacheable_state = $this->_push_cacheable_state('function', $tag_command);
        $attrs = $this->_parse_attrs($tag_args);
        $_cache_attrs = '';
        $arg_list = $this->_compile_arg_list('function', $tag_command, $attrs, $_cache_attrs);
        $output = $this->_compile_plugin_call('function', $tag_command).'(array('.@implode(',', $arg_list)."), \$this)";
        if($tag_modifier != '')
		{
            $this->_parse_modifiers($output, $tag_modifier);
        }
        if($output != '')
		{
            $output =  '<?php ' . $_cacheable_state . $_cache_attrs . 'echo ' . $output . ';'
                . $this->_pop_cacheable_state('function', $tag_command) . "?>" . $this->_additional_newline;
        }
        return true;
    }
    /**
     * compile a registered object tag
     *
     * @param string $tag_command
     * @param array $attrs
     * @param string $tag_modifier
     * @return string
     */
    function _compile_registered_object_tag($tag_command, $attrs, $tag_modifier)
    {
        if (substr($tag_command, 0, 1) == '/')
		{
            $start_tag = false;
            $tag_command = substr($tag_command, 1);
        } else {
            $start_tag = true;
        }
        list($object, $obj_comp) = @explode('->', $tag_command);
        $arg_list = array();
        if(count($attrs))
		{
            $_assign_var = false;
            foreach ($attrs as $arg_name => $arg_value)
			{
                if($arg_name == 'assign')
				{
                    $_assign_var = $arg_value;
                    unset($attrs['assign']);
                    continue;
                }
                if (is_bool($arg_value))
                    $arg_value = $arg_value ? 'true' : 'false';
                $arg_list[] = "'$arg_name' => $arg_value";
            }
        }
        if($this->_reg_objects[$object][2])
		{
            // smarty object argument format
            $args = "array(".@implode(',', (array)$arg_list)."), \$this";
        } else {
            // traditional argument format
            $args = @implode(',', array_values($attrs));
            if (empty($args)) {
                $args = '';
            }
        }
        $prefix = '';
        $postfix = '';
        $newline = '';
        if(!is_object($this->_reg_objects[$object][0]))
		{
            $this->_trigger_fatal_error("registered '$object' is not an object" , $this->_current_file, $this->_current_line_no, __FILE__, __LINE__);
        } elseif(!empty($this->_reg_objects[$object][1]) && !in_array($obj_comp, $this->_reg_objects[$object][1])) {
            $this->_trigger_fatal_error("'$obj_comp' is not a registered component of object '$object'", $this->_current_file, $this->_current_line_no, __FILE__, __LINE__);
        } elseif(method_exists($this->_reg_objects[$object][0], $obj_comp))
		{
            // method
            if(in_array($obj_comp, $this->_reg_objects[$object][3]))
			{
                // block method
                if ($start_tag)
				{
                    $prefix = "\$this->_tag_stack[] = array('$obj_comp', $args); ";
                    $prefix .= "\$_block_repeat=true; \$this->_reg_objects['$object'][0]->$obj_comp(\$this->_tag_stack[count(\$this->_tag_stack)-1][1], null, \$this, \$_block_repeat); ";
                    $prefix .= "while (\$_block_repeat) { ob_start();";
                    $return = null;
                    $postfix = '';
                } else {
                    $prefix = "\$_obj_block_content = ob_get_contents(); ob_end_clean(); \$_block_repeat=false;";
                    $return = "\$this->_reg_objects['$object'][0]->$obj_comp(\$this->_tag_stack[count(\$this->_tag_stack)-1][1], \$_obj_block_content, \$this, \$_block_repeat)";
                    $postfix = "} array_pop(\$this->_tag_stack);";
                }
            } else {
                // non-block method
                $return = "\$this->_reg_objects['$object'][0]->$obj_comp($args)";
            }
        } else {
            // property
            $return = "\$this->_reg_objects['$object'][0]->$obj_comp";
        }
        if($return != null)
		{
            if($tag_modifier != '')
			{
                $this->_parse_modifiers($return, $tag_modifier);
            }
            if(!empty($_assign_var))
			{
                $output = "\$this->assign('" . $this->_dequote($_assign_var) ."',  $return);";
            } else {
                $output = 'echo ' . $return . ';';
                $newline = $this->_additional_newline;
            }
        } else {
            $output = '';
        }
        return '<?php ' . $prefix . $output . $postfix . "?>" . $newline;
    }
    /**
     * Compile {insert ...} tag
     *
     * @param string $tag_args
     * @return string
     */
    function _compile_insert_tag($tag_args)
    {
        $attrs = $this->_parse_attrs($tag_args);
        $name = $this->_dequote($attrs['name']);
        if (empty($name))
		{
            return $this->_syntax_error("missing insert name", E_USER_ERROR, __FILE__, __LINE__);
        }
        if (!preg_match('~^\w+$~', $name))
		{
            return $this->_syntax_error("'insert: 'name' must be an insert function name", E_USER_ERROR, __FILE__, __LINE__);
        }
        if (!empty($attrs['script']))
		{
            $delayed_loading = true;
        } else {
            $delayed_loading = false;
        }
        foreach ($attrs as $arg_name => $arg_value)
		{
            if (is_bool($arg_value))
                $arg_value = $arg_value ? 'true' : 'false';
            $arg_list[] = "'$arg_name' => $arg_value";
        }
        $this->_add_plugin('insert', $name, $delayed_loading);
        $_params = "array('args' => array(".@implode(', ', (array)$arg_list)."))";
        return "<?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');\necho smarty_core_run_insert_handler($_params, \$this); ?>" . $this->_additional_newline;
    }

    /**
     * Compile {include ...} tag
     *
     * @param string $tag_args
     * @return string
     */
    function _compile_include_tag($tag_args)
    {
        $attrs = $this->_parse_attrs($tag_args);
        $arg_list = array();
        if (empty($attrs['file']))
		{
            $this->_syntax_error("missing 'file' attribute in include tag", E_USER_ERROR, __FILE__, __LINE__);
        }
        foreach ($attrs as $arg_name => $arg_value)
		{
            if ($arg_name == 'file')
			{
                $include_file = $arg_value;
                continue;
            } else if ($arg_name == 'assign')
			{
                $assign_var = $arg_value;
                continue;
            }
            if (is_bool($arg_value))
                $arg_value = $arg_value ? 'true' : 'false';
				$arg_list[] = "'$arg_name' => $arg_value";
        }
        $output = '<?php ';
        if (isset($assign_var))
		{
            $output .= "ob_start();\n";
        }
        $output .=
            "\$_smarty_tpl_vars = \$this->_tpl_vars;\n";
        $_params = "array('smarty_include_tpl_file' => " . $include_file . ", 'smarty_include_vars' => array(".@implode(',', (array)$arg_list)."))";
        $output .= "\$this->_smarty_include($_params);\n" .
        "\$this->_tpl_vars = \$_smarty_tpl_vars;\n" .
        "unset(\$_smarty_tpl_vars);\n";
        if (isset($assign_var))
		{
            $output .= "\$this->assign(" . $assign_var . ", ob_get_contents()); ob_end_clean();\n";
        }
        $output .= ' ?>';
        return $output;
    }
    /**
     * Compile {include ...} tag
     *
     * @param string $tag_args
     * @return string
     */
    function _compile_include_php_tag($tag_args)
    {
        $attrs = $this->_parse_attrs($tag_args);
        if (empty($attrs['file']))
		{
            $this->_syntax_error("missing 'file' attribute in include_php tag", E_USER_ERROR, __FILE__, __LINE__);
        }
        $assign_var = (empty($attrs['assign'])) ? '' : $this->_dequote($attrs['assign']);
        $once_var = (empty($attrs['once']) || $attrs['once']=='false') ? 'false' : 'true';
        $arg_list = array();
        foreach($attrs as $arg_name => $arg_value)
		{
            if($arg_name != 'file' AND $arg_name != 'once' AND $arg_name != 'assign')
			{
                if(is_bool($arg_value))
                    $arg_value = $arg_value ? 'true' : 'false';
                $arg_list[] = "'$arg_name' => $arg_value";
            }
        }
        $_params = "array('smarty_file' => " . $attrs['file'] . ", 'smarty_assign' => '$assign_var', 'smarty_once' => $once_var, 'smarty_include_vars' => array(".@implode(',', $arg_list)."))";
        return "<?php require_once(SMARTY_CORE_DIR . 'core.smarty_include_php.php');\nsmarty_core_smarty_include_php($_params, \$this); ?>" . $this->_additional_newline;
    }
    /**
     * Compile {section ...} tag
     *
     * @param string $tag_args
     * @return string
     */
    function _compile_section_start($tag_args)
    {
        $attrs = $this->_parse_attrs($tag_args);
        $arg_list = array();
        $output = '<?php ';
        $section_name = $attrs['name'];
        if (empty($section_name)) {
            $this->_syntax_error("missing section name", E_USER_ERROR, __FILE__, __LINE__);
        }
        $output .= "unset(\$this->_sections[$section_name]);\n";
        $section_props = "\$this->_sections[$section_name]";
        foreach ($attrs as $attr_name => $attr_value)
		{
            switch ($attr_name)
			{
                case 'loop':
                    $output .= "{$section_props}['loop'] = is_array(\$_loop=$attr_value) ? count(\$_loop) : max(0, (int)\$_loop); unset(\$_loop);\n";
                    break;
                case 'show':
                    if (is_bool($attr_value))
                        $show_attr_value = $attr_value ? 'true' : 'false';
                    else
                        $show_attr_value = "(bool)$attr_value";
                    $output .= "{$section_props}['show'] = $show_attr_value;\n";
                    break;
                case 'name':
                    $output .= "{$section_props}['$attr_name'] = $attr_value;\n";
                    break;
                case 'max':
                case 'start':
                    $output .= "{$section_props}['$attr_name'] = (int)$attr_value;\n";
                    break;
                case 'step':
                    $output .= "{$section_props}['$attr_name'] = ((int)$attr_value) == 0 ? 1 : (int)$attr_value;\n";
                    break;
                default:
                    $this->_syntax_error("unknown section attribute - '$attr_name'", E_USER_ERROR, __FILE__, __LINE__);
                    break;
            }
        }
        if (!isset($attrs['show']))
            $output .= "{$section_props}['show'] = true;\n";

        if (!isset($attrs['loop']))
            $output .= "{$section_props}['loop'] = 1;\n";

        if (!isset($attrs['max']))
            $output .= "{$section_props}['max'] = {$section_props}['loop'];\n";
        else
            $output .= "if ({$section_props}['max'] < 0)\n" .
                       "    {$section_props}['max'] = {$section_props}['loop'];\n";

        if (!isset($attrs['step']))
            $output .= "{$section_props}['step'] = 1;\n";

        if (!isset($attrs['start']))
            $output .= "{$section_props}['start'] = {$section_props}['step'] > 0 ? 0 : {$section_props}['loop']-1;\n";
        else {
            $output .= "if ({$section_props}['start'] < 0)\n" .
                       "    {$section_props}['start'] = max({$section_props}['step'] > 0 ? 0 : -1, {$section_props}['loop'] + {$section_props}['start']);\n" .
                       "else\n" .
                       "    {$section_props}['start'] = min({$section_props}['start'], {$section_props}['step'] > 0 ? {$section_props}['loop'] : {$section_props}['loop']-1);\n";
        }
        $output .= "if ({$section_props}['show']) {\n";
        if (!isset($attrs['start']) && !isset($attrs['step']) && !isset($attrs['max']))
		{
            $output .= "    {$section_props}['total'] = {$section_props}['loop'];\n";
        } else {
            $output .= "    {$section_props}['total'] = min(ceil(({$section_props}['step'] > 0 ? {$section_props}['loop'] - {$section_props}['start'] : {$section_props}['start']+1)/abs({$section_props}['step'])), {$section_props}['max']);\n";
        }
        $output .= "    if ({$section_props}['total'] == 0)\n" .
                   "        {$section_props}['show'] = false;\n" .
                   "} else\n" .
                   "    {$section_props}['total'] = 0;\n";

        $output .= "if ({$section_props}['show']):\n";
        $output .= "
            for ({$section_props}['index'] = {$section_props}['start'], {$section_props}['iteration'] = 1;
                 {$section_props}['iteration'] <= {$section_props}['total'];
                 {$section_props}['index'] += {$section_props}['step'], {$section_props}['iteration']++):\n";
        $output .= "{$section_props}['rownum'] = {$section_props}['iteration'];\n";
        $output .= "{$section_props}['index_prev'] = {$section_props}['index'] - {$section_props}['step'];\n";
        $output .= "{$section_props}['index_next'] = {$section_props}['index'] + {$section_props}['step'];\n";
        $output .= "{$section_props}['first']      = ({$section_props}['iteration'] == 1);\n";
        $output .= "{$section_props}['last']       = ({$section_props}['iteration'] == {$section_props}['total']);\n";
        $output .= "?>";
        return $output;
    }
    /**
     * Compile {foreach ...} tag.
     *
     * @param string $tag_args
     * @return string
     */
    function _compile_foreach_start($tag_args)
    {
        $attrs = $this->_parse_attrs($tag_args);
        $arg_list = array();
        if (empty($attrs['from']))
		{
            return $this->_syntax_error("foreach: missing 'from' attribute", E_USER_ERROR, __FILE__, __LINE__);
        }
        $from = $attrs['from'];
        if (empty($attrs['item']))
		{
            return $this->_syntax_error("foreach: missing 'item' attribute", E_USER_ERROR, __FILE__, __LINE__);
        }

        $item = $this->_dequote($attrs['item']);

        if (!preg_match('~^\w+$~', $item))
		{
            return $this->_syntax_error("foreach: 'item' must be a variable name (literal string)", E_USER_ERROR, __FILE__, __LINE__);
        }
        if (isset($attrs['key']))
		{
            $key  = $this->_dequote($attrs['key']);
            if (!preg_match('~^\w+$~', $key))
			{
                return $this->_syntax_error("foreach: 'key' must to be a variable name (literal string)", E_USER_ERROR, __FILE__, __LINE__);
            }
            $key_part = "\$this->_tpl_vars['$key'] => ";
        } else {
            $key = null;
            $key_part = '';
        }
        if (isset($attrs['name']))
		{
            $name = $attrs['name'];
        } else {
            $name = null;
        }


        $output = '<?php ';
        $output .= "\$_from = $from; if (!is_array(\$_from) && !is_object(\$_from)) { settype(\$_from, 'array'); }";
        if (isset($name))
		{
            $foreach_props = "\$this->_foreach[$name]";
            $output .= "{$foreach_props} = array('total' => count(\$_from), 'iteration' => 0);\n";
            $output .= "if ({$foreach_props}['total'] > 0):\n";
            $output .= "    foreach (\$_from as $key_part\$this->_tpl_vars['$item']):\n";
            $output .= "        {$foreach_props}['iteration']++;\n";
        } else {
            $output .= "if (count(\$_from)):\n";
            $output .= "    foreach (\$_from as $key_part\$this->_tpl_vars['$item']):\n";

        }
        $output .= '?>';
        return $output;
    }
	function _complie_navmap_start($tag_args)
	{
		$paramer = $this->_parse_attrs($tag_args);
		$item = str_replace("'","",$paramer[item]);
		$path = dirname(dirname(dirname(__FILE__)));
		include($path."/plus/navmap.cache.php");
		global $db,$db_config,$config;
		if(is_array($navmap))
		{
			$ParamerArr = $this->GetSmarty($paramer,$_GET);
			$paramer = $ParamerArr[arr];
			$Purl =  $ParamerArr[purl];
		}
		
		$Navlist =$navmap[0];
		if(is_array($navmap))
		{
			foreach($navmap as $k=>$v)
			{
				foreach($Navlist as $key=>$val)
				{
					if($k==$val['id'])
					{
						foreach($v as $kk=>$value)
						{
							if($value['type']=='1')
							{
								if($config['sy_seo_rewrite']=="1" && $value['furl']!=''){
									$v[$kk]['url'] = $config['sy_weburl']."/".$value['furl'];
								}else{
									$v[$kk]['url'] = $config['sy_weburl']."/".$value['url'];
								}
							}
						}
						$Navlist[$key]['list']=$v;
					}
				}
			}
			foreach($Navlist as $key=>$value)
			{
				if($value['type']=='1')
				{
					if($config['sy_seo_rewrite']=="1" && $value['furl']!=''){
						$Navlist[$key]['url'] = $config['sy_weburl']."/".$value['furl'];
					}else{
						$Navlist[$key]['url'] = $config['sy_weburl']."/".$value['url'];
					}
				}
			}
		}
		$this->_tpl_vars[$item] = $Navlist;
		$tag_args = "from=\${$item} " . $tag_args;
		return $this->_compile_foreach_start($tag_args);
	}
	function _complie_nav_start($tag_args)
	{
		$paramer = $this->_parse_attrs($tag_args);
		$item = str_replace("'","",$paramer[item]);
		$path = dirname(dirname(dirname(__FILE__)));
		include($path."/plus/menu.cache.php");
		global $db,$db_config,$config;
		if(!$config)
		{
			include($path."/plus/config.php");
		}
		if(is_array($menu_name))
		{
			$ParamerArr = $this->GetSmarty($paramer,$_GET);
			$paramer = $ParamerArr[arr];
			$Purl =  $ParamerArr[purl];
			foreach($menu_name[12] as $key=>$val)
			{
				if($val['type']=='2')
				{
					if($config['sy_seo_rewrite']=="1" && $val[furl]!=''){
						$menu_name[12][$key][url] = $val[furl];
					}else{
						$menu_name[12][$key][url] = $val[url];
					}
					$menu_name[12][$key][navclass] = $this->navcalss($val,$paramer[hovclass]);
				}
			}
			foreach($menu_name[1] as $key=>$value)
			{
				if($value['type']=='1')
				{
					if($config['sy_seo_rewrite']=="1" && $value[furl]!=''){
						$menu_name[1][$key][url] = $config[sy_weburl]."/".$value[furl];
					}else{
						$menu_name[1][$key][url] = $config[sy_weburl]."/".$value[url];
					}
					$menu_name[1][$key][navclass] = $this->navcalss($value,$paramer[hovclass]);
				}
			}

			foreach($menu_name[2] as $key=>$value)
			{
				if($value['type']=='1')
				{
					if($config['sy_seo_rewrite']=="1" && $value[furl]!=''){
						$menu_name[2][$key][url] = $config[sy_weburl]."/".$value[furl];
					}else{
						$menu_name[2][$key][url] = $config[sy_weburl]."/".$value[url];
					}
					$menu_name[2][$key][navclass] = $this->navcalss($value,$paramer[hovclass]);
				}
			}
			foreach($menu_name[4] as $key=>$value)
			{
				if($value['type']=='1' && $value[furl]!='')
				{
					if($config['sy_seo_rewrite']=="1"){
						$menu_name[4][$key][url] = $config[sy_weburl]."/".$value[furl];
					}else{
						$menu_name[4][$key][url] = $config[sy_weburl]."/".$value[url];
					}
					$menu_name[4][$key][navclass] = $this->navcalss($value,$paramer[hovclass]);
				}
			}
			foreach($menu_name[5] as $key=>$value)
			{
				if($value['type']=='1' && $value[furl]!='')
				{
					if($config['sy_seo_rewrite']=="1"){
						$menu_name[5][$key][url] = $config[sy_weburl]."/".$value[furl];
					}else{
						$menu_name[5][$key][url] = $config[sy_weburl]."/".$value[url];
					}
					$menu_name[5][$key][navclass] = $this->navcalss($value,$paramer[hovclass]);
				}
			}
		}
		
		if($paramer[nid])
		{
			$Navlist =$menu_name[$paramer[nid]];
		}else{
			$Navlist =$menu_name[1];
		}

		$this->_tpl_vars[$item] = $Navlist;
		$tag_args = "from=\${$item} " . $tag_args;
		return $this->_compile_foreach_start($tag_args);
	}
	function navcalss($menu,$classname)
	{
		
		if($_GET['m'])
		{
			if($menu['url']=="news.html"){
				$menu['url']="index.php?m=news";
			}
			$navact = "m=".$_GET['m'];
		
			if(strpos($menu['url'],$navact)!==false)
			{
			
				if($_GET['part']=="1")
				{
					if(strpos($menu['url'],"part=1")!==false)
					{
						return $classname;
					}
				}else{
				
					if(strpos($menu['url'],"part=1")===false)
					{
						return $classname;
					}
				}
			}
		}else if($_GET['c'])
		{
			$navact = "c=".$_GET['c'];
			
			if(strpos($menu['url'],$navact)!==false)
			{
			
				if($_GET['part']=="1")
				{
					if(strpos($menu['url'],"part=1")!==false)
					{
						return $classname;
					}
				}else{
				
					if(strpos($menu['url'],"part=1")===false)
					{
						return $classname;
					}
				}
			}
		}else{
			
			if($menu['name']=="首页")
			{
				
				return $classname;
			}
		}
	}

	function _complie_fairs_start($tag_args)
	{
		$paramer = $this->_parse_attrs($tag_args);
		$item = str_replace("'","",$paramer[item]);
		global $db,$db_config,$config;
	
		$ParamerArr = $this->GetSmarty($paramer,$_GET);
		$paramer = $ParamerArr[arr];
		$Purl =  $ParamerArr[purl];
		$where = "1";
		$time = date("Y-m-d",time());
		
		if($paramer[state]=='1')
		{

			$where .=" AND `starttime`>'$time'";
		}elseif($paramer[state]=='2'){

			$where .=" AND `starttime`<'$time' AND `endtime`>'$time'";

		}elseif($paramer[state]=='3'){

			$where .=" AND `endtime`<'$time'";
		}
	
		if($paramer[order])
		{
			$where .= " ORDER BY $paramer[order] ";
		}else{
			$where .= " ORDER BY `starttime` ";
		}
		
		if($paramer[sort])
		{
			$where .= " $paramer[sort]";
		}else{
			$where .= " DESC ";
		}
		
		if($paramer[limit])
		{
			$limit=" LIMIT ".$paramer[limit];
		}else{
			$limit=" LIMIT 20";
		}
		if($paramer[ispage])
		{
			$limit = $this->PageNav($paramer,$_GET,"zhaopinhui",$where,$Purl);
		}
		$FairList=$db->select_all("zhaopinhui",$where.$limit);
		if(is_array($FairList))
		{
			foreach($FairList as $key=>$v)
			{
				$FairList[$key]["stime"]=strtotime($v[starttime])-mktime();
				$FairList[$key]["etime"]=strtotime($v[endtime])-mktime();
				if($paramer[len]){
					$FairList[$key]["title"]=mb_substr($v['title'],0,$paramer[len],"GBK");
				}
				$FairList[$key]["url"]=$this->Url("index","zph",array("c"=>'show',"id"=>$v['id']),"1");
			}
		}
		$this->_tpl_vars[$item] = $FairList;
		$tag_args = "from=\${$item} " . $tag_args;
		return $this->_compile_foreach_start($tag_args);
	}
 	function _complie_articleclass_start($tag_args)
	 {
		$paramer = $this->_parse_attrs($tag_args);
		$item = str_replace("'","",$paramer[item]);
		global $db,$db_config,$config;
		$ParamerArr = $this->GetSmarty($paramer,$_GET);
		$paramer = $ParamerArr[arr];
		$Purl =  $ParamerArr[purl];

		include APP_PATH."/plus/group.cache.php";

		if($paramer['classid']){
			$classid = @explode(',',$paramer['classid']);
			if(is_array($classid)){
				foreach($classid as $key=>$value)
				{
					$Info['id']   = $value;
					$Info['name'] = $group_name[$value];
					$group[] = $Info;
				}
			}
		}else if($paramer['rec']){
			if(is_array($group_rec)){
				foreach($group_rec as $key=>$value)
				{
					$Info['id']   = $value;
					$Info['name'] = $group_name[$value];
					$group[] = $Info;
				}
			}
		}else if($paramer['r_news']){
			if(is_array($group_recnews)){
				foreach($group_recnews as $key=>$value)
				{
					$Info['id']   = $value;
					$Info['name'] = $group_name[$value];
					$group[] = $Info;
				}
			}
		}else{
			if(is_array($group_index)){
				foreach($group_index as $key=>$value)
				{
					$Info['id']   = $value;
					$Info['name'] = $group_name[$value];
					$group[] = $Info;
				}
			}


		}

		


		if(is_array($group) )
		{
			foreach($group as $key=>$value)
			{
				if(intval($paramer[len])>0)
				{
					$len = intval($paramer[len]);
					$group[$key][name] = mb_substr($value[name],0,$len,"GBK");
				}
				if($group_type[$value['id']])
				{
					$nids = $value['id'].",".@implode(',',$group_type[$value['id']]);
				}else{
					$nids = $value['id'];
				}
				if($config[sy_news_rewrite]=="2"){
					$group[$key][url] = $config['sy_weburl']."/news/".$value[id]."/";
				}else{
					 $group[$key][url] = $this->Url("index",'news',array('c'=>'list',"nid"=>$value[id]),"1");
				}
				if($paramer[arcpic])
				{
					$query = $db->query("SELECT * FROM `$db_config[def]news_base` WHERE `nid`='$value[id]' AND `newsphoto`<>''  ORDER BY `sort` DESC,`datetime` DESC limit $paramer[arcpic]");
					while($rs = $db->fetch_array($query)){
						if(intval($paramer[pt_len])>0)
						{
							$len = intval($paramer[pt_len]);
							if($rs[color]){
								$rs[title] = "<font color='".$rs[color]."'>".mb_substr($rs[title],0,$len,"GBK")."</font>";
							}else{
								$rs[title] = mb_substr($rs[title],0,$len,"GBK");
							}
						}
						if(intval($paramer[pd_len])>0)
						{
							$len = intval($paramer[pd_len]);
							$rs[description] = mb_substr($rs[description],0,$len,"GBK");
						}
						foreach($group as $k=>$v)
						{
							if($v[id]==$rs[nid])
							{
								$rs[name] = $v[name];
							}
						}
					
						if($config[sy_news_rewrite]=="2"){
							$rs["url"]=$config['sy_weburl']."/news/".date("Ymd",$rs["datetime"])."/".$rs[id].".html";
						}else{
							$rs["url"] = $this->Url("index","news",array("c"=>"show","id"=>$rs[id]),"1");
						}
						$arcpic[] = $rs;
					}
					$group[$key][arcpic] = $arcpic;
					unset($arcpic);

				}
				if($paramer[arclist])
				{
					$query = $db->query("SELECT * FROM `$db_config[def]news_base` WHERE `nid`='$value[id]'  ORDER BY `sort` DESC,`datetime` DESC limit $paramer[arclist]");
					while($rs = $db->fetch_array($query))
					{
						if(intval($paramer[t_len])>0)
						{
							$len = intval($paramer[t_len]);
							if($rs[color]){
								$rs[title] = "<font color='".$rs[color]."'>".mb_substr($rs[title],0,$len,"GBK")."</font>";
							}else{
								$rs[title] = mb_substr($rs[title],0,$len,"GBK");
							}
						}
						if(intval($paramer[d_len])>0)
						{
							$len = intval($paramer[d_len]);
							$rs[description] = mb_substr($rs[description],0,$len,"GBK");
						}
						foreach($group as $k=>$v)
						{
							if($v[id]==$rs[nid])
							{
								$rs[name] = $v[name];
							}
						}
					
						if($config[sy_news_rewrite]=="2"){
							$rs["url"]=$config['sy_weburl']."/news/".date("Ymd",$rs["datetime"])."/".$rs[id].".html";
						}else{
							$rs["url"] = $this->Url("index","news",array("c"=>"show","id"=>$rs[id]),"1");
						}
						$arclist[] = $rs;
					}
					$group[$key][arclist] = $arclist;
					unset($arclist);
				}
			}
		}
	
		$this->_tpl_vars[$item] = $group;
	
		$tag_args = "from=\${$item} " . $tag_args;
	
		return $this->_compile_foreach_start($tag_args);
	 }

	 function _complie_userlist_start($tag_args)
	 {
		$paramer = $this->_parse_attrs($tag_args);
		$item = str_replace("'","",$paramer[item]);
		global $db,$db_config,$config;
		
		$ParamerArr = $this->GetSmarty($paramer,$_GET);
		$paramer = $ParamerArr['arr'];
		$Purl =  $ParamerArr['purl'];
		include APP_PATH."/plus/job.cache.php";
		$where = "a.status<>'2' and a.`r_status`<>'2' and b.`job_classid`<>'' and b.`open`='1' and a.`uid`=b.`uid`";
	
		if($config['sy_web_sit']=="1")
		{
			if($_SESSION['cityid']>0 && $_SESSION['cityid']!="")
			{
				$paramer['cityid']=$_SESSION['cityid'];
			}
			if($_SESSION['hyclass']>0 && $_SESSION['hyclass']!="")
			{
				$paramer['hy']=$_SESSION['hyclass'];
			}
		}
		
		if($paramer['where_uid']){
			$where .=" AND a.`uid` in (".$paramer['where_uid'].")";
		}
		if($paramer["idcard"]){
			$where .=" AND a.`idcard_status`='1'";
		}
		
	
		$where .=" AND  a.`def_job`=b.`id`";
		
		
		if($paramer["rec"])
		{
			$where .=" AND b.rec='1'";
		}
		
		if($paramer["rec_resume"])
		{
			$where .=" AND b.`rec_resume`='1'";
		}
		
		if($paramer['work'])
		{
			$show=$db->select_all("resume_show","1 group by eid","`eid`");
			if(is_array($show))
			{
				foreach($show as $v)
				{
					$eid[]=$v['eid'];
				}
			}
			$where .=" AND b.id in (".@implode(",",$eid).")";
		}
		
		if($paramer['cid'])
		{
			$where .= " AND (b.cityid='$paramer[cid]' or b.three_cityid='$paramer[cid]')";
		}
		
		if($paramer['keyword'])
		{
			$where1[]="b.`name` LIKE '%".$paramer['keyword']."%'";
			foreach($job_name as $k=>$v){
				if(strpos($v,$paramer['keyword'])!==false){
					$jobid[]=$k;
				}
			}
			if(is_array($jobid))
			{
				foreach($jobid as $value)
				{
					$class[]="FIND_IN_SET('".$value."',b.job_classid)";
				}
				$where1[]=@implode(" or ",$class);
			}
			include APP_PATH."/plus/city.cache.php";
			foreach($city_name as $k=>$v)
			{
				if(strpos($v,$paramer['keyword'])!==false)
				{
					$cityid[]=$k;
				}
			}
			if(is_array($cityid))
			{
				foreach($cityid as $value)
				{
					$class[]= "(b.provinceid = '".$value."' or b.cityid = '".$value."')";
				}
				$where1[]=@implode(" or ",$class);
			}
			$where.=" AND (".@implode(" or ",$where1).")";
		}
		
		if($paramer['pic']=="0"||$paramer['pic'])
		{
			$where .=" AND a.photo<>''";
		}
		
		if($paramer['name']=="0")
		{
			$where .=" AND a.name<>''";
		}
		
		if($paramer['hy']=="0")
		{
			$where .=" AND b.hy<>''";
		}elseif($paramer['hy']!=""){
			$where .= " AND (b.`hy` IN (".$paramer['hy']."))";
		}
		
		if($paramer['jobids'])
		{
			$joball=explode(",",$paramer['jobids']);
			foreach(explode(",",$paramer['jobids']) as $v)
			{
				if($job_type[$v]){
					$joball[]=@implode(",",$job_type[$v]);
				}
			}
			$job_classid=implode(",",$joball);
		}
		if($paramer['job1_son'])
		{
			$joball=$job_type[$paramer['job1_son']];
			foreach($job_type[$paramer['job1_son']] as $v)
			{
				$joball[]=@implode(",",$job_type[$v]);
			}
			$job_classid=@implode(",",$joball);
		}
		if($job_classid)
		{
			$classid=@explode(",",$job_classid);
			foreach($classid as $value)
			{
				$class[]="FIND_IN_SET('".$value."',b.job_classid)";
			}
			$classid=@implode(" or ",$class);
			$where .= " AND ($classid)";
		}
		if($paramer['job_post'])
		{
			foreach($paramer['job_post'] as $v)
			{
				$jobwhere[]="FIND_IN_SET('".$v."',b.job_classid)";
			}
			$jobwhere=implode(" or ",$jobwhere);
			$where .=" AND (".$jobwhere.")";
		}
		
		if($paramer['provinceid'])
		{
			$where .= " AND b.provinceid = '".$paramer['provinceid']."'";
		}
		
		if($paramer['cityid'])
		{
			$where .= " AND (b.`cityid` IN (".$paramer['cityid']."))";
		}
		
		if($paramer['three_cityid'])
		{
			$where .= " AND (b.`three_cityid` IN (".$paramer['three_cityid']."))";
		}
		
		if($paramer['cityin'])
		{
			$where .= " AND( AND b.provinceid IN (".$paramer['cityin'].") OR b.cityid IN (".$paramer['cityin'].") OR b.three_cityid IN (".$paramer['cityin']."))";
		}
		
		if($paramer['exp']){
			$where .=" AND a.exp='".$paramer['exp']."'";
		}else{
			$where .=" AND a.exp>'0'";
		}
		
		if($paramer['edu']){
			$where .=" AND a.edu='".$paramer['edu']."'";
		}else{
			$where .=" AND a.edu>'0'";
		}
		
		if($paramer['sex'])
		{
			$where .=" AND a.sex='".$paramer['sex']."'";
		}
		
		if($paramer['report'])
		{
			$where .=" AND b.report='".$paramer['report']."'";
		}
		
		if($paramer['salary'])
		{
			$where .=" AND b.salary='".$paramer['salary']."'";
		}
		
		if($paramer['type'])
		{
			$where .= " AND b.type='".$paramer['type']."'";
		}
		
		if($paramer['uptime'])
		{
			$time=time();
			$uptime = $time-$paramer['uptime']*86400;
			$where.=" AND b.lastupdate>'".$uptime."'";
		}
		
		if($paramer['adtime'])
		{
			$time=time();
			$adtime = $time-$paramer['adtime']*86400;
			$where.=" AND b.status_time>'".$adtime."'";
		}
		if($paramer['order'] && $paramer['order']!="lastdate"){
			if($paramer['order']=='ant_num'){
				$order = " ORDER BY a.`".str_replace("'","",$paramer['order'])."`";
			}elseif($paramer['order']=='topdate'){
				$nowtime=time();
				$order.=" ORDER BY if(b.topdate>$nowtime,b.topdate,b.lastupdate)";
			}else{
				$order = " ORDER BY b.`".str_replace("'","",$paramer['order'])."`";
			}
		}else{
			$order = " ORDER BY b.lastupdate ";
		}
	
		if($paramer[sort])
		{
			$sort = $paramer[sort];
		}else{
			$sort = " DESC";
		}
	
		if($paramer['limit'])
		{
			$limit=" LIMIT ".$paramer['limit'];
		}
		$where.=$order.$sort;
	
		if($paramer['where'])
		{
			$where = $paramer['where'];
		}
		if($paramer['ispage'])
		{
		
		
			$limit = $this->PageNav($paramer,$_GET,"resume",$where,$Purl,"resume_expect");
			
		}
		$user=$db->select_alls("resume","resume_expect",$where.$limit,"b.*,a.*,a.name as username,b.provinceid as provinceid,b.cityid as cityid");
		if(is_array($user))
		{
			
			$cache_array = $db->cacheget();
			$userclass_name = $cache_array["user_classname"];
			$city_name      = $cache_array["city_name"];
			$job_name		= $cache_array["job_name"];
			$industry_name	= $cache_array["industry_name"];
			$my_down=array();
			if($_COOKIE['usertype']=='2')
			{
				$my_down=$db->select_all("down_resume","`comid`='".$_COOKIE['uid']."'","uid");
			}
			foreach($user as $k=>$v)
			{
				$time=time()-$v['lastupdate'];
				if($time>86400 && $time<259300){
					$user[$k]['time'] = ceil($time/86400)."天前";
				}elseif($time>3600 && $time<86400){
					$user[$k]['time'] = ceil($time/3600)."小时前";
				}elseif($time>60 && $time<3600){
					$user[$k]['time'] = ceil($time/60)."分钟前";
				}elseif($time<60){
					$user[$k]['time'] = "刚刚";
				}else{
					$user[$k]['time'] = date("Y-m-d",$v['lastupdate']);
				}

				if($config['sy_usertype_1']=='1'&&$v['photo']){
					if(!empty($my_down)){
						foreach($my_down as $m_k=>$m_v){
							$my_down_uid[]=$m_v['uid'];
						}
						if(in_array($v['uid'],$my_down_uid)==false){
							$user[$k]['photo']='./'.$config['member_logo'];
						}
					}else{
						$user[$k]['photo']='./'.$config['member_logo'];
					}
				}
				if($config["user_name"]==3)
				{
					$user[$k]["username_n"] = "NO.".$v["id"];
				}elseif($config["user_name"]==2){
					if($v["sex"]=='6'){
						$user[$k]["username_n"] = mb_substr($v["username"],0,2)."先生";
					}else{
						$user[$k]["username_n"] = mb_substr($v["username"],0,2)."女士";
					}
				}else{
					$user[$k]["username_n"] = $v["username"];
				}
				$a=date('Y',strtotime($user[$k]['birthday']));
				$user[$k]['age']=date("Y")-$a;
				$user[$k]['sex_n']=$userclass_name[$v['sex']];
				$user[$k]['edu_n']=$userclass_name[$v['edu']];
				$user[$k]['exp_n']=$userclass_name[$v['exp']];
				$user[$k]['job_city_one']=$city_name[$v['provinceid']];
				$user[$k]['job_city_two']=$city_name[$v['cityid']];
				$user[$k]['job_city_three']=$city_name[$v['three_cityid']];
				$user[$k]['salary_n']=$userclass_name[$v['salary']];
				$user[$k]['report_n']=$userclass_name[$v['report']];
				$user[$k]['type_n']=$userclass_name[$v['type']];
				$user[$k]['lastupdate']=date("Y-m-d",$v['lastupdate']);
				
				if($paramer['top']){
					$m_name=$db->select_only($db_config[def]."member","`uid`='".$v['uid']."'","username");
					$user[$k]['m_name']=$m_name[0]['username'];
					$user[$k]['user_url']=$this->Furl(array("url"=>"c:profile,id:".$v['uid']));
				}else{
					$user[$k]['user_url']=$this->Url("index","resume",array("id"=>$v['id']),"1");
				}
				$user[$k]["hy_info"]=$industry_name[$v['hy']];
				$job_classid=@explode(",",$v['job_classid']);
				if(is_array($job_classid))
				{
					foreach($job_classid as $val)
					{
						$jobname[]=$job_name[$val];
					}
				}
				$user[$k]['job_post']=@implode(",",$jobname);
			
				if($paramer['post_len'])
				{
					$postname[$k]=@implode(",",$jobname);
					$user[$k]['job_post_n']=mb_substr($postname[$k],0,$paramer[post_len],"GBK");
				}
				if($paramer['keyword'])
				{
					$user[$k]['username']=str_replace($paramer['keyword'],"<font color=#FF6600 >".$paramer['keyword']."</font>",$v['username']);
					$user[$k]['job_post']=str_replace($paramer['keyword'],"<font color=#FF6600 >".$paramer['keyword']."</font>",$user[$k]['job_post']);
					$user[$k]['job_post_n']=str_replace($paramer['keyword'],"<font color=#FF6600 >".$paramer['keyword']."</font>",$user[$k]['job_post_n']);
					$user[$k]['job_city_one']=str_replace($paramer['keyword'],"<font color=#FF6600 >".$paramer['keyword']."</font>",$city_name[$v['provinceid']]);
					$user[$k]['job_city_two']=str_replace($paramer['keyword'],"<font color=#FF6600 >".$paramer['keyword']."</font>",$city_name[$v['cityid']]);
				}
				$jobname=array();
			}
			if($paramer['keyword']!=""&&!empty($user))
			{
				$this->addkeywords('5',$paramer['keyword']);
			}
		}
		$this->_tpl_vars[$item] = $user;
		$tag_args = "from=\${$item} " . $tag_args;
		return $this->_compile_foreach_start($tag_args);
	 }

	 function _complie_key_start($tag_args)
	 {
		$paramer = $this->_parse_attrs($tag_args);
		$item = str_replace("'","",$paramer[item]);
		global $db,$db_config,$config;
		$where = "`check`='1'";
		
		if($paramer[recom])
		{
			$tuijian = 1;
		}
		
		if($paramer[type]){
			$type = $paramer[type];
		}

		
		if($paramer[limit])
		{
			$limit=$paramer[limit];
		}else{
			$limit=20;
		}
		include APP_PATH."/plus/keyword.cache.php";


		if($paramer['iswap'])
		{
			$wap = "/wap";
		}else{
			$index =1;
		}
		if(is_array($keyword))
		{
			if($paramer['iswap'])
			{
				$i=0;
				foreach($keyword as $k=>$v)
				{
					if($tuijian && $v['tuijian']!='1')
					{
						continue;
					}
					if($type && $v['type']!=$type)
					{
						continue;
					}
					$i++;
					if($v['type']=="1"){
						$v['url'] = $config['sy_weburl'].$wap."/index.php?m=once&keyword=".$v['key_name'];
						$v['type_name']='一句话招聘';
					}elseif($v['type']=="3"){
						$v['url'] = $config['sy_weburl'].$wap."/index.php?m=com&keyword=".$v['key_name'];
						$v['type_name']='职位';
					}elseif($v['type']=="4"){
						$v['url'] = $config['sy_weburl'].$wap."/index.php?m=firm&keyword=".$v['key_name'];
						$v['type_name']='公司';
					}elseif($v['type']=="5"){
						$v['url'] = $config['sy_weburl'].$wap."/index.php?m=user&c=search&keyword=".$v['key_name'];
						$v['type_name']='人才';
					}
					$v['key_title']=$v['key_name'];
					if($v['color']){
						$v['key_name']="<font color=\"".$v['color']."\">".$v['key_name']."</font>";
					}
					$list[] = $v;
					if($i==$limit)
					{
						break;
					}
				}
			}else{
				$i=0;
				foreach($keyword as $k=>$v)
				{
					if($tuijian && $v['tuijian']!='1')
					{
						continue;
					}
					if($type && $v['type']!=$type)
					{
						continue;
					}
					$i++;
					if($v['type']=="1"){
						$v['url'] = $config['sy_weburl']."/index.php?m=once&keyword=".$v['key_name'];
						$v['type_name']='一句话招聘';
					}elseif($v['type']=="3"){
						$v['url'] = $config['sy_weburl']."/index.php?m=com&c=search&keyword=".$v['key_name'];
						$v['type_name']='职位';
					}elseif($v['type']=="4"){
						$v['url'] = $config['sy_weburl']."/index.php?m=firm&keyword=".$v['key_name'];
						$v['type_name']='公司';
					}elseif($v['type']=="5"){
						$v['url'] = $config['sy_weburl']."/index.php?m=user&c=search&keyword=".$v['key_name'];
						$v['type_name']='人才';
					}
					$v['key_title']=$v['key_name'];
					if($v['color']){
						$v['key_name']="<font color=\"".$v['color']."\">".$v['key_name']."</font>";
					}
					$list[] = $v;
					if($i==$limit)
					{
						break;
					}
				}
			}
		}
		$this->_tpl_vars[$item] = $list;
		$tag_args = "from=\${$item} " . $tag_args;
		return $this->_compile_foreach_start($tag_args);
	 }
	 function _complie_fast_start($tag_args)
	 {
		$paramer = $this->_parse_attrs($tag_args);
		$item = str_replace("'","",$paramer[item]);
		global $db,$db_config,$config;
		
		$ParamerArr = $this->GetSmarty($paramer,$_GET);
		$paramer = $ParamerArr[arr];
		$Purl =  $ParamerArr[purl];
		$where = "`status`='1'  and `edate`>'".time()."'";
		
		if($paramer[keyword])
		{
			$where.=" AND `title` LIKE '%".$paramer[keyword]."%' or `companyname` LIKE '%".$paramer[keyword]."%'";
		}
		if($paramer['delid'])
		{
			$where.=" AND `id`<>'".$paramer['delid']."'";
		}
		if($paramer[order])
		{
			$order = " ORDER BY `".str_replace("'","",$paramer[order])."`";
		}else{
			$order = " ORDER BY ctime ";
		}
		
		if($paramer[sort])
		{
			$sort = $paramer[sort];
		}else{
			$sort = " DESC";
		}
		
		if($paramer[limit])
		{
			$limit=" LIMIT ".$paramer[limit];
		}else{
			$limit=" LIMIT 20";
		}
		
		if($paramer[where])
		{
			$where = $paramer[where];
		}
		if($paramer[ispage])
		{
			$limit = $this->PageNav($paramer,$_GET,"once_job",$where,$Purl);
		}
		$where.=$order.$sort.$limit;
		$list=$db->select_all("once_job",$where);
		if(is_array($list)){
			foreach($list as $key=>$value)
			{
				$time=time()-$value['ctime'];
				if($time>86400 && $time<604800){
					$list[$key]['ctime'] = ceil($time/86400)."天前";
				}elseif($time>3600 && $time<86400){
					$list[$key]['ctime'] = ceil($time/3600)."小时前";
				}elseif($time>60 && $time<3600){
					$list[$key]['ctime'] = ceil($time/60)."分钟前";
				}elseif($time<60){
					$list[$key]['ctime'] = "刚刚";
				}else{
					$list[$key]['ctime'] = date("Y-m-d",$value['ctime']);
				}
			}
			if($paramer[keyword]!=""&&!empty($list)){
				$this->addkeywords('1',$paramer[keyword]);
			}
		}
		$this->_tpl_vars[$item] = $list;
		$tag_args = "from=\${$item} " . $tag_args;
		return $this->_compile_foreach_start($tag_args);
	 }
	function _complie_tiny_start($tag_args)
	 {
		$paramer = $this->_parse_attrs($tag_args);
		$item = str_replace("'","",$paramer[item]);
		global $db,$db_config,$config;
		include APP_PATH."/plus/user.cache.php";
		

		$ParamerArr = $this->GetSmarty($paramer,$_GET);
		$paramer = $ParamerArr[arr];
		$Purl =  $ParamerArr[purl];
		$where = "status='1' ";
		
		if($paramer[keyword])
		{
			$where.=" AND `username` LIKE '%".$paramer[keyword]."%' or `job` LIKE '%".$paramer[keyword]."%'";
		}
		if($paramer['add_time']>0)
		{
			$time=time()-$paramer['add_time']*86400;
			$where.=" and `time`>'".$time."'";
		}
		if($paramer['delid'])
		{
			$where.=" AND `id`<>'".$paramer['delid']."'";
		}
		if($paramer['order'])
		{
			$order = " ORDER BY `".str_replace("'","",$paramer[order])."`";
		}else{
			$order = " ORDER BY time ";
		}
		
		if($paramer['sort'])
		{
			$sort = $paramer['sort'];
		}else{
			$sort = " DESC";
		}
		
		if($paramer[limit])
		{
			$limit=" LIMIT ".$paramer[limit];
		}else{
			$limit=" LIMIT 20";
		}
		
		if($paramer[where])
		{
			$where = $paramer[where];
		}
		if($paramer[ispage])
		{

			$limit = $this->PageNav($paramer,$_GET,"resume_tiny",$where,$Purl);
		}
		$where.=$order.$sort.$limit;
		$list=$db->select_all("resume_tiny",$where);
		if(is_array($list))
		{
			foreach($list as $key=>$value)
			{
				$time=time()-$value['time'];
				if($time>86400 && $time<604800){
					$list[$key]['time'] = ceil($time/86400)."天前";
				}elseif($time>3600 && $time<86400){
					$list[$key]['time'] = ceil($time/3600)."小时前";
				}elseif($time>60 && $time<3600){
					$list[$key]['time'] = ceil($time/60)."分钟前";
				}elseif($time<60){
					$list[$key]['time'] = "刚刚";
				}else{
					$list[$key]['time'] = date("Y-m-d",$value['time']);
				}
				$list[$key]['sex_name'] =$userclass_name[$value['sex']];
				$list[$key]['exp_name'] =$userclass_name[$value['exp']];
			}
		}
		if(is_array($list))
		{
			if($paramer[keyword]!=""&&!empty($list)){
				$this->addkeywords('1',$paramer[keyword]);
			}
		}
		$this->_tpl_vars[$item] = $list;
		$tag_args = "from=\${$item} " . $tag_args;
		return $this->_compile_foreach_start($tag_args);
	 }
	 function _complie_comlist_start($tag_args)
	 {
		$paramer = $this->_parse_attrs($tag_args);
		$item = str_replace("'","",$paramer['item']);
		global $db,$db_config,$config;
		
		if($config['sy_web_site']=="1")
		{
			if($_SESSION['cityid']>0 && $_SESSION['cityid']!="")
			{
				$paramer['cityid']=$_SESSION['cityid'];
			}
			if($_SESSION['hyclass']>0 && $_SESSION['hyclass']!="")
			{
				$paramer['hy']=$_SESSION['hyclass'];
			}
		}
		$time = time();
		
		$ParamerArr = $this->GetSmarty($paramer,$_GET);
		$paramer = $ParamerArr['arr'];
		$Purl =  $ParamerArr['purl'];
		$where=1;
		if(!is_array($this->company_rating))
		{
			$comrat = $db->select_all($db_config['def']."company_rating");
			$this->company_rating=$comrat;
		}else{
			$comrat = $this->company_rating;
		}
		
		if($paramer['keyword'])
		{
			$where.=" AND `name` LIKE '%".$paramer['keyword']."%'";
		}
		
		if($paramer['hy'])
		{
			$where .= " AND `hy` = '".$paramer['hy']."'";
		}
		
		if($paramer['pr'])
		{
			$where .= " AND `pr` = '".$paramer['pr']."'";
		}
		
		if($paramer['mun'])
		{
			$where .= " AND `mun` = '".$paramer['mun']."'";
		}
		
		if($paramer['provinceid'])
		{
			$where .= " AND `provinceid` = '".$paramer['provinceid']."'";
		}
		
		if($paramer['cityid'])
		{
			$where .= " AND `cityid` = '".$paramer['cityid']."'";
		}
		
		if($paramer['linkman'])
		{
			$where .= " AND `linkman`<>''";
		}
		
		if($paramer['linktel'])
		{
			$where .= " AND `linktel`<>''";
		}
		
		if($paramer['linkmail'])
		{
			$where .= " AND `linkmail`<>''";
		}
		
		if($paramer['logo'])
		{
			$where .= " AND `logo`<>''";
		}
		
		if($paramer['r_status'])
		{
			$where .= " AND `r_status`='".$paramer['r_status']."'";
		}else{
			$where .= " AND `r_status`<>'2'";
		}
		
		if($paramer['cert'])
		{
			$where .= " AND `yyzz_status`='1'";
		}
		
		if($paramer['uptime'])
		{
			$uptime = $time-$paramer['uptime']*3600;
			$where.=" AND `lastupdate`>'".$uptime."'";
		}
		if($paramer['jobtime'])
		{
			$where.=" AND `jobtime`<>''";
		}
		
		if($paramer['rec'])
		{
			$where.=" AND `rec`='1'";
		}
		
		if($paramer['order'])
		{
			if($paramer['order']=="lastＵpdate"){
				$paramer['order']="lastupdate";
			}
			$order = " ORDER BY `".$paramer['order']."`  ";
		}else{
			$order = " ORDER BY `jobtime` ";
		}
		
		if($paramer['sort'])
		{
			$sort = $paramer['sort'];
		}else{
			$sort = " DESC";
		}
		
		if($paramer['limit'])
		{
			$limit.=" limit ".$paramer['limit'];
		}
		$where.=$order.$sort;
		
		if($paramer['where'])
		{
			$where = $paramer['where'];
		}
		
		$cache_array = $db->cacheget();
		if($paramer['ispage'])
		{
			if($paramer['rec']==1)
			{
				$limit = $this->PageNav($paramer,$_GET,"company",$where,$Purl,"","1");
			}else{
				$limit = $this->PageNav($paramer,$_GET,"company",$where,$Purl);
			}
			$this->_tpl_vars['firmurl'] = $ParamerArr['firmurl'];
		}
		$Query = $db->query("SELECT * FROM $db_config[def]company where ".$where.$limit);
		while($rs = $db->fetch_array($Query)){
			$ComList[] = $db->array_action($rs,$cache_array);
			$ListId[] = $rs['uid'];
		}
		
		if($paramer['ismsg'])
		{
			$Msgid = @implode(",",$ListId);
			$msglist = $db->select_alls("company_msg","resume","a.`cuid` in ($Msgid) and a.`uid`=b.`uid` order by a.`id` desc","a.cuid,a.content,b.name,b.photo,b.def_job");
			if(is_array($ListId) && is_array($msglist))
			{
				foreach($ComList as $key=>$value)
				{
					foreach($msglist as $k=>$v)
					{
						if($value['uid']==$v['cuid'])
						{
							$ComList[$key]['msg'][$k]['content'] = $v['content'];
							$ComList[$key]['msg'][$k]['name'] = $v['name'];
							$ComList[$key]['msg'][$k]['photo'] = $v['photo'];
							$ComList[$key]['msg'][$k]['eid'] = $v['def_job'];
						}
					}
				}
			}
		}
		
		if($paramer['isjob'])
		{
		
			$JobId = @implode(",",$ListId);
			$JobList=$db->select_only("(select * from `".$db_config[def]."company_job` order by `lastupdate` desc) `temp`","`uid` IN ($JobId) and `edate`>'".mktime()."' and r_status<>'2' and status<>'1' and state=1  order by `lastupdate` desc");
			if(is_array($ListId) && is_array($JobList))
			{
				foreach($ComList as $key=>$value)
				{
					$ComList[$key]['jobnum'] = 0;
					foreach($JobList as $k=>$v)
					{
						if($value['uid']==$v['uid'])
						{
							$id = $v['id'];
							$ComList[$key]['newsjob'] = $v['name'];
							$ComList[$key]['newsjob_status'] = $v['status'];
							$ComList[$key]['r_status'] = $v['r_status'];

							$ComList[$key]['job_url'] = $this->Url("index","com",array("c"=>"comapply","id"=>$v['id']),"1");
							$v = $db->array_action($value,$cache_array);
							$v['id']= $id;
							$v['name'] = $ComList[$key]['newsjob'];
							$ComList[$key]['joblist'][] = $v;
							$ComList[$key]['jobnum'] = $ComList[$key]['jobnum']+1;
						}
					}
					foreach($comrat as $k=>$v){
						if($value['rating']==$v['id'])
						{
							$ComList[$key]['color'] = $v['com_color'];
							$ComList[$key]['ratlogo'] = $v['com_pic'];
						}
					}
				}
			}
		}
	
		if($paramer['isnews'])
		{
		
			$JobId = @implode(",",$ListId);
			$NewsList=$db->select_all("company_news","`uid` IN ($JobId) and status=1  order by `id` desc");
			if(is_array($ListId) && is_array($NewsList))
			{
				foreach($ComList as $key=>$value)
				{
					$ComList[$key]['newsnum'] = 0;
					foreach($NewsList as $k=>$v)
					{
						if($value['uid']==$v['uid'])
						{
							$ComList[$key]['newslist'][] = $v;
							$ComList[$key]['newsnum'] = $ComList[$key]['newsnum']+1;
						}
					}
				}
			}
		}
	
		if($paramer['isshow'])
		{
		
			$JobId = @implode(",",$ListId);
			$ShowList=$db->select_all("company_show","`uid` IN ($JobId) order by `id` desc");
			if(is_array($ListId) && is_array($ShowList))
			{
				foreach($ComList as $key=>$value)
				{
					$ComList[$key]['shownum'] = 0;
					foreach($ShowList as $k=>$v)
					{
						if($value['uid']==$v['uid'])
						{
							$ComList[$key]['showlist'][] = $v;
							$ComList[$key]['shownum'] = $ComList[$key]['shownum']+1;
						}
					}
				}
			}
		}
	
	
		if($paramer['firm']){
			$atnlist = $db->select_all("atn","`uid`='$_COOKIE[uid]'");
			if(is_array($atnlist) && is_array($ComList)){
				foreach($ComList as $key=>$value){
					if(!empty($atnlist)){
						foreach($atnlist as $v){
							if($value['uid'] == $v['sc_uid']){
								$ComList[$key]['atn'] = "取消关注";
								break;
							}else{
								$ComList[$key]['atn'] = "关注";
							}
						}
					}else{
						$ComList[$key]['atn'] = "关注";
					}
				}
			}
		}
		if(is_array($ComList)){
			foreach($ComList as $key=>$value){
				$ComList[$key]['com_url'] = $this->Curl(array("url"=>"id:".$value['uid']));
				$ComList[$key]['joball_url'] = $this->Curl(array("url"=>"id:".$value['uid'].",tp:post"));
				if($value['logo']!=""){
					$ComList[$key]['logo'] = str_replace("./",$config['sy_weburl']."/",$value['logo']);
				}else{
					$ComList[$key]['logo'] = $config['sy_weburl']."/".$config['sy_unit_icon'];
				}
			}
			if($paramer['keyword']!=""&&!empty($ComList)){
				$this->addkeywords('4',$paramer['keyword']);
			}
		}
		$this->_tpl_vars[$item] = $ComList;
		$tag_args = "from=\${$item} " . $tag_args;
		return $this->_compile_foreach_start($tag_args);
	 }
	 function _complie_singlenav_start($tag_args)
	 {
		$paramer = $this->_parse_attrs($tag_args);
		$item = str_replace("'","",$paramer[item]);
		global $db,$db_config,$config;
		$Query = $db->query("SELECT * FROM $db_config[def]description where is_nav='1' order by sort asc");
		while($rs = $db->fetch_array($Query))
		{
			$rs['url']=$config['sy_weburl']."/".$rs['url'];
			$List[] =  $rs;
		}
		$this->_tpl_vars[$item] = $List;
		$tag_args = "from=\${$item} " . $tag_args;
		return $this->_compile_foreach_start($tag_args);
     }

	 function _complie_maplist_start($tag_args)
	 {
		$paramer = $this->_parse_attrs($tag_args);
		$item = str_replace("'","",$paramer[item]);
		global $db,$db_config,$config;
		
		if($config[sy_web_site]=="1")
		{
			if($_SESSION[cityid]>0 && $_SESSION[cityid]!="")
			{
				$paramer[cityid]=$_SESSION[cityid];
			}
			if($_SESSION[three_cityid]>0 && $_SESSION[three_cityid]!="")
			{
				$paramer[three_cityid] = $_SESSION[three_cityid];
			}
			if($_SESSION[hyclass]>0 && $_SESSION[hyclass]!="")
			{
				$paramer[hy]=$_SESSION[hyclass];
			}
		}
		$time = time();
		
		$ParamerArr = $this->GetSmarty($paramer,$_GET);
		$paramer = $ParamerArr[arr];
		$Purl =  $ParamerArr[purl];
		$where=1;
		$xy=getAround($paramer[x],$paramer[y],$paramer[r]);
		if($xy[0])
		{
			$where.=" AND `x`>='".$xy[0]."' AND `x`<='".$xy[1]."' AND `y`>='".$xy[3]."' AND `y`<='".$xy[2]."'";
		}
		
		if($paramer[keyword])
		{
			$where.=" AND `name` LIKE '%".$paramer[keyword]."%'";
		}
		
		if($paramer[hy])
		{
			$where .= " AND `hy` = '".$paramer[hy]."'";
		}
		
		if($paramer[pr])
		{
			$where .= " AND `pr` = '".$paramer[pr]."'";
		}
		
		if($paramer[mun])
		{
			$where .= " AND `mun` = '".$paramer[mun]."'";
		}
		
		if($paramer[provinceid])
		{
			$where .= " AND `provinceid` = '".$paramer[provinceid]."'";
		}
		
		if($paramer[cityid])
		{
			$where .= " AND (`cityid` = '".$paramer[cityid]."' or `provinceid` = '".$paramer[cityid]."')";
		}
		
		if($paramer[three_cityid])
		{
			$where .= " AND `three_cityid` = '".$paramer[three_cityid]."'";
		}
		
		if($paramer[linkman])
		{
			$where .= " AND `linkman`<>''";
		}
	
		if($paramer[linktel])
		{
			$where .= " AND `linktel`<>''";
		}
	
		if($paramer[linkmail])
		{
			$where .= " AND `linkmail`<>''";
		}
		
		if($paramer[logo])
		{
			$where .= " AND `logo`<>''";
		}
		if($paramer['lastupdate'])
		{
			$lastupdate = $time-$paramer['lastupdate']*86400;
			$where.=" AND `lastupdate`>'".$lastupdate."'";
		}
		if($paramer['r_status'])
		{
			$where .= " AND `r_status`='".$paramer['r_status']."'";
		}else{
			$where .= " AND `r_status`<>'2'";
		}
		if($paramer['cert'])
		{
			$where .= " AND `yyzz_status`='1'";
		}
		if($paramer[jobtime])
		{
			$where.=" AND `jobtime`<>''";
		}
		
		$jobwhere=1;
		if($paramer[job1])
		{
			$jobwhere.=" AND `job1`='$paramer[job1]'";
		}
		if($paramer[job1_son])
		{
			$jobwhere.=" AND `job1_son`='$paramer[job1_son]'";
		}
		if($paramer[job_post])
		{
			$jobwhere.=" AND `job_post`='$paramer[job_post]'";
		}
		$joball=$db->select_all("company_job",$jobwhere);
		if(is_array($joball)){
			foreach($joball as $v){
				$uid[]=$v[uid];
			}
			$uid=@implode(",",$uid);
			$where.=" and `uid` in ($uid)";
		}
		
		if($paramer[order])
		{
			$order = " ORDER BY `".$paramer[order]."`  ";
		}else{
			$order = " ORDER BY `jobtime` ";
		}
		
		if($paramer[sort])
		{
			$sort = $paramer[sort];
		}else{
			$sort = " DESC";
		}
		
		if($paramer[limit])
		{
			$limit.=" limit ".$paramer[limit];
		}
		$where.=$order.$sort;
		
		if($paramer[where])
		{
			$where = $paramer[where];
		}
	
		$cache_array = $db->cacheget();

		if($paramer[ispage])
		{
			$limit = $this->PageNav($paramer,$_GET,"company","`x`<>'' and ".$where,$Purl);
		}
		$Query = $db->query("SELECT * FROM $db_config[def]company where x<>'' and ".$where.$limit);
		while($rs = $db->fetch_array($Query)){
			$ComList[] = $db->array_action($rs,$cache_array);
			$ListId[] =  $rs[uid];
		}
	  

		
		if($paramer[isjob])
		{
			
			$JobId = @implode(",",$ListId);
			$JobList=$db->select_only("(select * from `".$db_config[def]."company_job` order by `lastupdate` desc) `temp`","`uid` IN ($JobId) and `edate`>'".mktime()."' and r_status<>'2' and status<>'1' and state=1 order by `lastupdate` desc");
			 if(is_array($ListId) && is_array($JobList))
			{
				foreach($ComList as $key=>$value)
				{
					$ComList[$key][jobnum] = 0;
					foreach($JobList as $k=>$v)
					{
						if($value[uid]==$v[uid])
						{
							$ComList[$key][newsjob] = $v[name];
							$ComList[$key][newsjob_status] = $v[status];
							$ComList[$key][r_status] = $v[r_status];
							$ComList[$key][job_url] = $this->Url("index","com",array("c"=>"comapply","id"=>$v[id]),"1");
							$jobv = $db->array_action($v,$cache_array);
							$jobv['name'] = $ComList[$key][newsjob];
							$jobv[job_url] = $this->Url("index","com",array("c"=>"comapply","id"=>$v[id]),"1");
							$ComList[$key][joblist][] = $jobv;
							$ComList[$key][jobnum] = $ComList[$key][jobnum]+1;
						}
					}
				}
			}
		}
		if(is_array($ComList))
		{
			$num=0;
			foreach($ComList as $key=>$value)
			{
				$ComList[$key][com_url] = $this->Curl(array("url"=>"id:".$value[uid]));
				$ComList[$key][joball_url] = $this->Curl(array("url"=>"id:".$value[uid].",tp:post"));
				$ComList[$key]['orderid']=$num;
				if(!$value[x] && !$value[y])
				{
					$address=$value[job_city_one].$value[job_city_two].$value[address];
					$xydata=@file_get_contents("http://api.map.baidu.com/geocoder?address=".$address."&output=json&key=37492c0ee6f924cb5e934fa08c6b1676");
					$name=json_decode($xydata);
					$ComList[$key][x]=$name->result->location->lng;
					$ComList[$key][y]=$name->result->location->lat;
				}
				$num++;
			}
			if($paramer[keyword]!=""&&!empty($ComList))
			{
				$this->addkeywords('4',$paramer[keyword]);
			}
		}
		$this->_tpl_vars[$item] = $ComList;
		$tag_args = "from=\${$item} " . $tag_args;
		return $this->_compile_foreach_start($tag_args);
	 }

	 function _complie_hotjob_start($tag_args)
	 {
		$paramer = $this->_parse_attrs($tag_args);
		$item = str_replace("'","",$paramer[item]);
		global $db,$db_config,$config;
		$time = time();
		$where = "`time_start`<$time AND `time_end`>$time";
		
		if($paramer[order])
		{
			$order = " ORDER BY `".$paramer[order]."`  ";
		}else{
			$order = " ORDER BY `sort` ";
		}
		
		if($paramer[sort])
		{
			$sort = $paramer[sort];
		}else{
			$sort = " ASC";
		}
	
		if($paramer[limit])
		{
			$limit.=" LIMIT ".$paramer[limit];
		}
		$where.=$order.$sort;
		
		if($paramer[where])
		{
			$where = $paramer[where];
		}
		
		if($paramer[ispage])
		{
			$limit = $this->PageNav($paramer,$_GET,"hotjob",$where,$Purl);
		}
		$Query = $db->query("SELECT * FROM $db_config[def]hotjob where ".$where.$limit);
		
		while($rs = $db->fetch_array($Query)){

			$ComList[] = $rs;
			$ListId[] =  $rs[uid];
		}
	
		$jobwhere=1;
		if($config[sy_web_site]=="1")
		{
			if($_SESSION[cityid]>0 && $_SESSION[cityid]!="")
			{
				$jobwhere.=" and `cityid`='$_SESSION[cityid]'";
			}
			if($_SESSION[three_cityid]>0 && $_SESSION[three_cityid]!="")
			{
				$jobwhere.=" and `three_cityid`='$_SESSION[three_cityid]'";
			}
			if($_SESSION[hyclass]>0 && $_SESSION[hyclass]!="")
			{
				$jobwhere.=" and `hy`='".$_SESSION[hyclass]."'";
			}
		}
		
		$JobId = @implode(",",$ListId);
		$JobList=$db->select_all("company_job","`uid` IN ($JobId) and `edate`>'".mktime()."' and state=1 and r_status<>'2' and status<>'1' and $jobwhere");
		$statis=$db->select_all("company_statis","`uid` IN ($JobId)","`uid`,`comtpl`");
		if(is_array($ListId))
		{
			
			$cache_array = $db->cacheget();
			foreach($ComList as $key=>$value)
			{
				$i=0;
				if(is_array($JobList))
				{
					$ComList[$key]["job"].="<div class=\"area_left\"> ";
					foreach($JobList as $k=>$v)
					{
						if($value[uid]==$v[uid] && $i<5)
						{
							$job_url = $this->url("index","com",array("c"=>"comapply","id"=>"$v[id]"),"1");
							$v[name] = mb_substr($v[name],0,10,"GBK");
							$ComList[$key]["job"].="<a href='".$job_url."'>".$v[name]."</a>";
							$i++;
						}
					}
					foreach($statis as $v)
					{
						if($value['uid']==$v['uid'])
						{
							if($v['comtpl'] && $v['comtpl']!="default"){
								$jobs_url = $this->Curl(array("url"=>"id:".$value[uid]))."#job";
							}else{
								$jobs_url = $this->Curl(array("url"=>"tp:post,id:".$value[uid]));
							}
						}
					}
					$com_url = $this->Curl(array("url"=>"id:".$value[uid]));
					$beizhu=mb_substr($value['beizhu'],0,50,"GBK")."...";
					$ComList[$key]["job"].="</div><div class=\"area_right\"><a href='".$com_url."'>".$value["username"]."</a>".$beizhu."</div><div class=\"area_left_bot\"><a href='".$jobs_url."'>全部职位</a></div><div class='area_right_bot'><a href='".$com_url."'>公司详情</a></div>";

					$ComList[$key]["url"]=$com_url;
				}
			}
		}
		$this->_tpl_vars[$item] = $ComList;
		$tag_args = "from=\${$item} " . $tag_args;
		return $this->_compile_foreach_start($tag_args);
	 }
	 function _complie_comjob_start($tag_args)
	 {
		$paramer = $this->_parse_attrs($tag_args);
		$item = str_replace("'","",$paramer[item]);
		global $db,$db_config,$config;
		if($config['sy_web_site']=="1")
		{
			if($_SESSION['cityid']>0 && $_SESSION['cityid']!="")
			{
				$paramer['cityid']=$_SESSION['cityid'];
			}
			if($_SESSION['three_cityid']>0 && $_SESSION['three_cityid']!="")
			{
				$paramer['three_cityid'] = $_SESSION['three_cityid'];
			}
			if($_SESSION['hyclass']>0 && $_SESSION['hyclass']!="")
			{
				$paramer['hy']=$_SESSION['hyclass'];
			}
		}

		$ParamerArr = $this->GetSmarty($paramer,$_GET);
		$paramer = $ParamerArr[arr];
		$Purl =  $ParamerArr[purl];
		$time = time();
		$where = "`sdate`<$time AND `edate`>$time and  `state`='1' and `r_status`<>'2' and `status`<>'1'";
		if($paramer['urgent'])
		{
			$where.=" AND `urgent_time`>$time";
		}
		if($paramer['cityid'])
		{
			$where.=" AND `cityid`='".$paramer['cityid']."'";
		}
		if($paramer['three_cityid'])
		{
			$where.=" AND `three_cityid`='".$paramer['three_cityid']."'";
		}
		if($paramer['rec'])
		{
			$where.=" AND `rec_time`>$time";
		}
		if($paramer['limit'])
		{
			$limit =  " limit $paramer[limit]";
		}
		include APP_PATH."/plus/city.cache.php";
		$query = $db->query("select count(*) as num,uid,provinceid,cityid,three_cityid,lastupdate from $db_config[def]company_job where  $where GROUP BY uid ORDER BY lastupdate desc $limit");

		while($rs = $db->fetch_array($query))
		{
			if($paramer['citylen'])
			{
				$one_city[$rs['uid']]		= mb_substr($city_name[$rs['cityid']],0,$paramer['citylen']);
				$two_city[$rs['uid']]  = mb_substr($city_name[$rs['three_cityid']],0,$paramer['citylen']);
			}else{
				$one_city[$rs['uid']]			= $city_name[$rs['cityid']];
				$two_city[$rs['uid']]  = $city_name[$rs['three_cityid']];
			}
			if($one_city[$rs['uid']]==''){
				$one_city[$rs['uid']]=$city_name[$rs['provinceid']];
			} 
			$lasttime[$rs['uid']] = date('Y-m-d',$rs['lastupdate']);
			$uids[] = $rs['uid'];
		}
		if(!empty($uids))
		{
			$comids = @implode(',',$uids);
			$joblist = $db->select_all("company_job","$where AND `uid` IN ($comids) ORDER BY lastupdate desc");

			foreach($joblist as $value)
			{
				if(!$paramer['jobnum'] || count($job_list[$value['uid']])<$paramer['jobnum'])
				{
					if($paramer['joblen'])
					{
						$value['name_n'] = mb_substr($value['name'],0,$paramer['joblen'],'gbk');
					}
					$value['url'] = $this->Url("index","com",array("c"=>"comapply","id"=>$value['id']),"1");
					$job_list[$value['uid']][] = $value;
				}
				$comname[$value['uid']] = $value['com_name'];
			}

			foreach($uids as $key=>$value){
				$ComList[$key]['curl']     = $this->Curl(array("url"=>"id:".$value));
				$ComList[$key]['uid']     = $value;
				$ComList[$key]['name']	  = $comname[$value];
				$ComList[$key]['one_city']	  = $one_city[$value];
				$ComList[$key]['two_city']	  = $two_city[$value];
				$ComList[$key]['lasttime']     = $lasttime[$value];
				if($paramer['comlen'])
				{
					$ComList[$key]['name_n'] = mb_substr($comname[$value],0,$paramer['comlen'],'gbk');
				}
				$ComList[$key]['joblist'] =$job_list[$value];
			}
		}

		$this->_tpl_vars[$item] = $ComList;
		$tag_args = "from=\${$item} " . $tag_args;
		return $this->_compile_foreach_start($tag_args);

	 }
	 /**
     * Compile {article ...} tag.
     *
     * @param string $tag_args
     * @return string
     */
	 function _complie_article_start($tag_args)
	 {
		$paramer = $this->_parse_attrs($tag_args);
		$item = str_replace("'","",$paramer[item]);
		global $db,$db_config,$config;
		include APP_PATH."/plus/group.cache.php";

		$ParamerArr = $this->GetSmarty($paramer,$_GET);
		$paramer = $ParamerArr[arr];
		$Purl =  $ParamerArr[purl];
		$where=1;
		if($_SESSION['did']){
			$where.=" and (FIND_IN_SET('".$_SESSION['did']."',did) or FIND_IN_SET('0',did))";
		}else{
			$where.=" and `did`='0'";
		}
		if(is_array($paramer))
		{
		
			if($paramer[nid]!=""){
				$where .=" AND `nid` in (".$paramer[nid].")";
				$nids = @explode(',',$paramer['nid']);
			} else if($paramer[rec]!=""){
				include APP_PATH."/plus/group.cache.php";
				if(is_array($group_rec)){
					foreach($group_rec as $key=>$value){
						if($key<=2){
							$nids[]=$value;
						}
					}
					$paramer[nid]=@implode(',',$nids);
				}
			}
			if($paramer[nid])
			{
				$nid_s = @explode(',',$paramer[nid]);
				
				foreach($nid_s as $v)
				{
					if($group_type[$v])
					{
						$paramer[nid] = $paramer[nid].",".@implode(',',$group_type[$v]);
					}
				}
			}
			if($paramer[type])
			{
				$type = str_replace("\"","",$paramer[type]);
				$type_arr =	@explode(",",$type);
			
				if(is_array($type_arr) && !empty($type_arr))
				{
					foreach($type_arr as $key=>$value)
					{
						$where .=" AND FIND_IN_SET('".$value."',`describe`)";
						if(count($nids)>0)
						{
							$picwhere .=" AND FIND_IN_SET('".$value."',`describe`)";
						}
					}
				}
			}
		
			if($paramer[pic]!="")
			{
				$where .=" AND `newsphoto`<>''";
			}
			
			if($paramer[order]!="")
			{
				$order = str_replace("'","",$paramer[order]);
				$where .=" ORDER BY $order";
			}else{
				$where .=" ORDER BY `datetime`";
			}
			
			if($paramer[sort])
			{
				$where.=" ".$paramer[sort];
			}else{
				$where.=" DESC";
			}
			
			if(intval($paramer[limit])>0)
			{
				$limit = intval($paramer[limit]);
				$limit = " limit $limit";
			}else{
				$paramer[limit] = 20;
			}
			if($paramer[ispage])
			{
				$limit = $this->PageNav($paramer,$_GET,"news_base",$where,$Purl,"","5");
			}
		}


		
		if(!$paramer[ispage] && count($nids)>0)
		{
			$where = " and nid IN (".$paramer['nid'].")";
			
			if($paramer['pic'])
			{
				if(!$paramer['piclimit'])
				{
					$piclimit = 1;
				}else{
					$piclimit = $paramer['piclimit'];
				}
				$db->query("set @f=0,@n=0");
				$query = $db->query("select * from (select id,title,color,datetime,description,newsphoto,@n:=if(@f=nid,@n:=@n+1,1) as aid,@f:=nid as nid from $db_config[def]news_base  WHERE `nid` IN (".$paramer['nid'].") AND `newsphoto` <>''  order by nid asc,datetime desc) a where aid <=".$piclimit);

				while($rs = $db->fetch_array($query))
				{
					
					if(intval($paramer[t_len])>0)
					{
						$len = intval($paramer[t_len]);
						if($rs[color]){
							$rs[title] = "<font color='".$rs[color]."'>".mb_substr($rs[title],0,$len,"GBK")."</font>";
						}else{
							$rs[title] = mb_substr($rs[title],0,$len,"GBK");
						}
					}
					
					if(intval($paramer[d_len])>0)
					{
						$len = intval($paramer[d_len]);
						$rs[description] = mb_substr($rs[description],0,$len,"GBK");
					}
					$rs['name'] = $group_name[$rs['nid']];

					
					if($config[sy_news_rewrite]=="2"){
						$rs["url"]=$config['sy_weburl']."/news/".date("Ymd",$rs["datetime"])."/".$rs[id].".html";
					}else{

						$rs["url"] = $this->Url("index","news",array("c"=>"show","id"=>$rs[id]),"1");
					}
					$rs[time]=date("Y-m-d",$rs[datetime]);
					$rs['datetime']=date("m-d",$rs[datetime]);
					$List[$rs['nid']]['pic'][] = $rs;
				}
			}

				$db->query("set @f=0,@n=0");
				$query = $db->query("select * from (select id,title,datetime,color,description,newsphoto,@n:=if(@f=nid,@n:=@n+1,1) as aid,@f:=nid as nid from $db_config[def]news_base  WHERE `nid` IN (".$paramer['nid'].") order by nid asc,datetime desc) a where aid <=".$paramer['limit']);

				while($rs = $db->fetch_array($query))
				{
					
					if(intval($paramer[t_len])>0)
					{
						$len = intval($paramer[t_len]);
						if($rs[color]){
							$rs[title] = "<font color='".$rs[color]."'>".mb_substr($rs[title],0,$len,"GBK")."</font>";
						}else{
							$rs[title] = mb_substr($rs[title],0,$len,"GBK");
						}
					}
					
					if(intval($paramer[d_len])>0)
					{
						$len = intval($paramer[d_len]);
						$rs[description] = mb_substr($rs[description],0,$len,"GBK");
					}
					
					$rs['name'] = $group_name[$rs['nid']];
					
					if($config[sy_news_rewrite]=="2"){
						$rs["url"]=$config['sy_weburl']."/news/".date("Ymd",$rs["datetime"])."/".$rs[id].".html";
					}else{
						$rs["url"] = $this->Url("index","news",array("c"=>"show","id"=>$rs[id]),"1");
					}
					$rs[time]=date("Y-m-d",$rs[datetime]);
					$rs[datetime]=date("m-d",$rs[datetime]);
					$List[$rs['nid']]['arclist'][] = $rs;
				}

		}else{
			$query = $db->query("SELECT * FROM `$db_config[def]news_base` WHERE ".$where.$limit);
			while($rs = $db->fetch_array($query))
			{

					if(intval($paramer[t_len])>0)
					{
						$len = intval($paramer[t_len]);
						$rs[title] = mb_substr($rs[title],0,$len,"GBK");
					}
					
					if(intval($paramer[d_len])>0)
					{
						$len = intval($paramer[d_len]);
						$rs[description] = mb_substr($rs[description],0,$len,"GBK");
					}
					
					$rs['name'] = $group_name[$rs['nid']];
					
					if($config[sy_news_rewrite]=="2"){
						$rs["url"]=$config['sy_weburl']."/news/".date("Ymd",$rs["datetime"])."/".$rs[id].".html";
					}else{
						$rs["url"] = $this->Url("index","news",array("c"=>"show","id"=>$rs[id]),"1");
					}
					$rs[time]=date("Y-m-d",$rs[datetime]);
					$rs[datetime]=date("m-d",$rs[datetime]);
					$List[] = $rs;

				}
		}
			$this->_tpl_vars[$item] = $List;
			$tag_args = "from=\${$item} " . $tag_args;
			return $this->_compile_foreach_start($tag_args);
	 }
	  function _complie_msglist_start($tag_args)
	 {
		$paramer = $this->_parse_attrs($tag_args);
		$item = str_replace("'","",$paramer[item]);
		$time=time();
		global $db,$db_config,$config;
		$ParamerArr = $this->GetSmarty($paramer,$_GET);
		$paramer = $ParamerArr[arr];
		$Purl =  $ParamerArr[purl];
		$path = dirname(dirname(dirname(__FILE__)));
		$where = "`reply`<>'' and `del_status`='0'";
		if($paramer[id])
		{
			$where.=" and `jobid`='$paramer[id]'";
		}
		
		if($paramer[order])
		{
			$where.="  ORDER BY `".$paramer[order]."`";
		}else{
			$where.="  ORDER BY `datetime`";
		}
		
		if($paramer[sort])
		{
			$where.=" ".$paramer[sort];
		}else{
			$where.=" DESC";
		}
		if($paramer[limit])
		{
			$limit=" LIMIT ".$paramer[limit];
		}else{
			$limit=" LIMIT 20";
		}
		if($paramer[ispage])
		{
			$limit = $this->PageNav($paramer,$_GET,"msg",$where,$Purl);
		}
		$list=$db->select_all("msg",$where.$limit);
		$user=$db->select_all("resume","","uid,def_job");
		if(is_array($list))
		{
			foreach($list as $key=>$value)
			{
				foreach($user as $v)
				{
					if($value[uid]==$v[uid])
					{
						$list[$key][user_url] = $this->Url("index","resume",array("id"=>$v[def_job]),"1");
					}
				}
				$list[$key][datetime]=date("Y-m-d",$value[datetime]);
				$list[$key][reply_time]=date("Y-m-d",$value[reply_time]);
			}
		}
		$this->_tpl_vars[$item] = $list;
		$tag_args = "from=\${$item} " . $tag_args;
		return $this->_compile_foreach_start($tag_args);
	 }
	  function _complie_announcement_start($tag_args)
	 {
		$paramer = $this->_parse_attrs($tag_args);
		$item = str_replace("'","",$paramer[item]);
		$time=time();
		global $db,$db_config,$config;
		$ParamerArr = $this->GetSmarty($paramer,$_GET);
		$paramer = $ParamerArr[arr];
		$Purl =  $ParamerArr[purl];
		$path = dirname(dirname(dirname(__FILE__)));
		$where = 1;
		
		if($_SESSION['did']){
			$where.=" and (FIND_IN_SET('".$_SESSION['did']."',did) or FIND_IN_SET('0',did))";
		}else{
			$where.=" and `did`='0'";
		}
		
		if($paramer[order])
		{
			$where.="  ORDER BY `".$paramer[order]."`";
		}else{
			$where.="  ORDER BY `datetime`";
		}
		
		if($paramer[sort])
		{
			$where.=" ".$paramer[sort];
		}else{
			$where.=" DESC";
		}
		if($paramer[limit])
		{
			$limit=" LIMIT ".$paramer[limit];
		}else{
			$limit=" LIMIT 20";
		}
		if($paramer[ispage])
		{
			$limit = $this->PageNav($paramer,$_GET,"msg",$where,$Purl);
		}
		$list=$db->select_all("admin_announcement",$where.$limit);
		if(is_array($list))
		{
			foreach($list as $key=>$value)
			{
				
				if($paramer[t_len])
				{
					$list[$key][title_n] = mb_substr($value['title'],0,$paramer[t_len],"GBK");
				}
				$list[$key][time]=date("Y-m-d",$value[datetime]);
				$list[$key][url] = $this->Url("index","announcement",array("id"=>$value[id]),"1");
			}
		}
		$this->_tpl_vars[$item] = $list;
		$tag_args = "from=\${$item} " . $tag_args;
		return $this->_compile_foreach_start($tag_args);
	 }

	
	 function _complie_downlist_start($tag_args){
	 	$paramer = $this->_parse_attrs($tag_args);
		$item = str_replace("'","",$paramer[item]);
		global $db,$db_config,$config;
		$path = dirname(dirname(dirname(__FILE__)));
		$where="1";
		if($paramer['order'])
		{
			$where.=" ORDER BY `".$paramer['order']."`";
		}else{
			$where.=" ORDER BY `id`";
		}
		if($paramer['sort'])
		{
			$where.=" ".$paramer['sort'];
		}else{
			$where.=" DESC";
		}
		if($paramer['limit'])
		{
			$limit=" LIMIT ".$paramer['limit'];
		}else{
			$limit=" LIMIT 10";
		}
		$list=$db->select_all("down_resume",$where.$limit);
		if($list&&is_array($list)){
			$uids=$comids=array();
			foreach($list as $val){
				$uids[]=$val['uid'];
				$comids[]=$val['comid'];
			}
			$resume=$db->select_all("resume","`uid` in(".@implode(',',$uids).")","`uid`,`name`");
			$company=$db->select_all("company","`uid` in(".@implode(',',$comids).")","`uid`,`name`");
			foreach($list as $key=>$val){
				$time=time()-$val['downtime'];
				if($time>86400 && $time<259300){
					$list[$key]['time'] = ceil($time/86400)."天前";
				}elseif($time>3600 && $time<86400){
					$list[$key]['time'] = ceil($time/3600)."小时前";
				}elseif($time>60 && $time<3600){
					$list[$key]['time'] = ceil($time/60)."分钟前";
				}elseif($time<60){
					$list[$key]['time'] = "刚刚";
				}else{
					$list[$key]['time'] = date("Y-m-d",$val['downtime']);
				}
				foreach($resume as $v){
					if($v['uid']==$val['uid']){
						$list[$key]['username']=$v['name'];
					}
				}
				foreach($company as $value){
					if($val['comid']==$value['uid']){
						$list[$key]['comname']=$value['name'];
					}
				}
				if($paramer['user_len']){
					$list[$key]['username']=mb_substr($list[$key]['username'],0,$paramer['user_len'],"GBK");
				}
				if($paramer['com_len']){
					$list[$key]['comname']=mb_substr($list[$key]['comname'],0,$paramer['com_len'],"GBK");
				}
				$list[$key]['curl']=$this->Curl(array("url"=>"id:".$val[comid]));
				$list[$key]['url']=$this->Url("index","resume",array("id"=>$val['eid']),"1");
			}
		}
		$this->_tpl_vars[$item] = $list;
		$tag_args = "from=\${$item} " . $tag_args;
		return $this->_compile_foreach_start($tag_args);

	 }
	 function _complie_link_start($tag_args)
	 {
		$paramer = $this->_parse_attrs($tag_args);
		$item = str_replace("'","",$paramer[item]);
		global $db,$db_config,$config;
		$path = dirname(dirname(dirname(__FILE__)));

		
		$domain='';
		if($_SESSION["cityid"]!="" || $_SESSION["hyclass"]!="")
		{
			include(PLUS_PATH."domain_cache.php");
			$host_url=$_SERVER['HTTP_HOST'];
			foreach($site_domain as $v)
			{
				if($v['host']==$host_url)
				{
					$domain=$v['id'];
				}
			}
		}
		
		if($paramer[tem_type])
		{
			$tem_type = $paramer[tem_type];

		}else{
			$tem_type = 1;
		}
		
		if($paramer[type])
		{
			$where .= " AND `link_type`='".$paramer[type]."'";
		}

		include APP_PATH."/plus/link.cache.php";


		if(is_array($link))
		{
			$i=0;
			foreach($link as $key=>$value)
			{
				if($value['domain']!='0' && stripos($value['domain'],$domain)===false)
				{
					continue;
				}elseif($paramer['tem_type'] && $value['tem_type']!=$paramer['tem_type'] && $value['tem_type']!='1'){
					continue;

				}elseif((!$paramer['tem_type'] || $paramer['tem_type']=='1') && $value['tem_type']!='1'){

					continue;
				}
				if($paramer['type'] && $value['link_type']!=$paramer['type'])
				{
					continue;
				}
				if($paramer['limit'] && $paramer['limit']<=$i)
				{
					break;
				}
				$value[picurl] = $config[sy_weburl]."/".$value[pic];
				$linkList[] = $value;
				$i++;
			}

		}

		$this->_tpl_vars[$item] = $linkList;
		$tag_args = "from=\${$item} " . $tag_args;
		return $this->_compile_foreach_start($tag_args);
	 }
	
	function _complie_hrclass_start($tag_args)
	{
		$paramer = $this->_parse_attrs($tag_args);
		$item = str_replace("'","",$paramer[item]);
		global $db,$db_config,$config;
		
		$ParamerArr = $this->GetSmarty($paramer,$_GET);
		$paramer = $ParamerArr[arr];
		$Purl =  $ParamerArr[purl];
		$where = "1";
		
		if($paramer[limit])
		{
			$limit=" LIMIT ".$paramer[limit];
		}else{
			$limit=" LIMIT 10";
		}
		if($paramer[ispage])
		{
			$limit = $this->PageNav($paramer,$_GET,"toolbox_class",$where,$Purl);
		}
		$List=$db->select_all("toolbox_class",$where.$limit);
		$this->_tpl_vars[$item] = $List;
		$tag_args = "from=\${$item} " . $tag_args;
		return $this->_compile_foreach_start($tag_args);
	}
	
	function _complie_hrlist_start($tag_args)
	{
		$paramer = $this->_parse_attrs($tag_args);
		$item = str_replace("'","",$paramer[item]);
		global $db,$db_config,$config;
		
		$ParamerArr = $this->GetSmarty($paramer,$_GET);
		$paramer = $ParamerArr[arr];
		$Purl =  $ParamerArr[purl];
		$where = "`is_show`='1'";
		if($paramer['id'])
		{
			$where.=" and `cid`='".$paramer['id']."'";
		}
		
		if($paramer['keyword'])
		{
			$where.=" AND `name` LIKE '%".$paramer['keyword']."%'";
		}
		
		if($paramer[order])
		{
			$where.="  ORDER BY `".$paramer['order']."`";
		}else{
			$where.="  ORDER BY `id`";
		}
		
		if($paramer['sort'])
		{
			$where.=" ".$paramer['sort'];
		}else{
			$where.=" DESC";
		}
		
		if($paramer['limit'])
		{
			$limit=" LIMIT ".$paramer['limit'];
		}
		$List=$db->select_all("toolbox_doc",$where.$limit);
		$this->_tpl_vars[$item] = $List;
		$tag_args = "from=\${$item} " . $tag_args;
		return $this->_compile_foreach_start($tag_args);
	}
	
    function _complie_joblist_start($tag_args)
    {
		$paramer = $this->_parse_attrs($tag_args);
		$item = str_replace("'","",$paramer[item]);
		global $db,$db_config,$config;
		$path = dirname(dirname(dirname(__FILE__)));
		$class_id = $paramer[class_id];
		$time = time();

		if($config[sy_web_site]=="1")
		{
			if($_SESSION[cityid]>0 && $_SESSION[cityid]!="")
			{
				$paramer[cityid] = $_SESSION[cityid];
			}
			if($_SESSION[three_cityid]>0 && $_SESSION[three_cityid]!="")
			{
				$paramer[three_cityid] = $_SESSION[three_cityid];
			}
			if($_SESSION[hyclass]>0 && $_SESSION[hyclass]!="")
			{
				$paramer[hy]=$_SESSION[hyclass];
			}
		}
		
		$ParamerArr = $this->GetSmarty($paramer,$_GET);
		$paramer = $ParamerArr[arr];
		 $Purl =  $ParamerArr[purl];
		if($paramer[sdate]){
			$where = "`sdate`>'".strtotime("-".intval($paramer[sdate])." day",time())."' and `edate`>'$time' and `state`='1'";
		}else{
			$where = "`edate`>'$time' and `state`='1'";
		}
		
		if($paramer[uid])
		{
			$where .= " AND `uid` = '".$paramer[uid]."'";
		}
	
		if($paramer[rec])
		{
			$where.=" AND `rec_time`>'".time()."'";
		}
		if($paramer['cert'])
		{
			$company=$db->select_all("company","`yyzz_status`='1'","`uid`");
			if(is_array($company))
			{
				foreach($company as $v)
				{
					$job_uid[]=$v['uid'];
				}
			}
			$where.=" and `uid` in (".@implode(",",$job_uid).")";
		}
		
		if($paramer[noid]){
			$where.= " and `id`<>'".$paramer[noid]."'";
		}
		
		if($paramer[r_status]){
			$where.= " and `r_status`='2'";
		}else{
			$where.= " and `r_status`<>'2'";
		}
		
		if($paramer[status]){
			$where.= " and `status`='1'";
		}else{
			$where.= " and `status`<>'1'";
		}
		
		if($paramer[pr])
		{
			$where .= " AND `pr` = '".$paramer[pr]."'";
		}
		
		if($paramer['hy'])
		{
			$where .= " AND `hy` = '".$paramer['hy']."'";
		}
		
		if($paramer[mun])
		{
			$where .= " AND `mun` = '".$paramer[mun]."'";
		}
		
		if($paramer[job1])
		{
			$where .= " AND `job1` = '".$paramer[job1]."'";
		}
		
		if($paramer[job1_son])
		{
			$where .= " AND `job1_son` = '".$paramer[job1_son]."'";
		}
		
		if($paramer[job_post])
		{
			$where .= " AND (`job_post` IN (".$paramer['job_post']."))";
		}
		
		if($paramer['jobwhere']){
			$where .=" and ".$paramer['jobwhere'];
		}
		if($paramer['jobids'])
		{
			$where.= " AND (`job1_son`='".$paramer['jobids']."' OR `job_post`='".$paramer['jobids']."')";
		}
		if($paramer['jobin'])
		{
			$where .= " AND (`job1` IN (".$paramer['jobin'].") OR `job1_son` IN (".$paramer['jobin'].") OR `job_post` IN (".$paramer['jobin']."))";
		}
		if($paramer[provinceid])
		{
			$where .= " AND `provinceid` = '".$paramer[provinceid]."'";
		}
	
		if($paramer['cityid'])
		{
			$where .= " AND (`cityid` IN (".$paramer['cityid']."))";
		}
		
		if($paramer['three_cityid'])
		{
			$where .= " AND (`three_cityid` IN (".$paramer['three_cityid']."))";
		}
		
		if($paramer[edu])
		{
			$where .= " AND `edu` = '".$paramer[edu]."'";
		}
		
		if($paramer[exp])
		{
			$where .= " AND `exp` = '".$paramer[exp]."'";
		}
		
		if($paramer[type])
		{
			$where .= " AND `type` = '".$paramer[type]."'";
		}
		
		if($paramer[sex])
		{
			$where .= " AND `sex` = '".$paramer[sex]."'";
		}
		
		if($paramer[salary])
		{
			$where .= " AND `salary` = '".$paramer[salary]."'";
		}
		
		if($paramer[cityin])
		{
			$where .= " AND( AND `provinceid` IN (".$paramer[cityin].") OR `cityid` IN (".$paramer[cityin].") OR `three_cityid` IN (".$paramer[cityin]."))";
		}
		
		if($paramer[urgent])
		{
			$where.=" AND `urgent_time`>'".time()."'";
		}
	
		if($paramer[uptime])
		{
			$uptime = $time-$paramer[uptime]*86400;
			$where.=" AND `lastupdate`>'".$uptime."'";
		}
		
		if($paramer[comname])
		{
			$where.=" AND `com_name` LIKE '%".$paramer[comname]."%'";
		}
		
		if($paramer[com_pro])
		{
			$where.=" AND `com_provinceid` ='".$paramer[com_pro]."'";
		}
		
		if($paramer[keyword])
		{
			$where1[]="`name` LIKE '%".$paramer[keyword]."%'";
			$where1[]="`com_name` LIKE '%".$paramer[keyword]."%'";
			include APP_PATH."/plus/city.cache.php";
			foreach($city_name as $k=>$v)
			{
				if(strpos($v,$paramer[keyword])!==false)
				{
					$cityid[]=$k;
				}
			}
			if(is_array($cityid))
			{
				foreach($cityid as $value)
				{
					$class[]= "(provinceid = '".$value."' or cityid = '".$value."')";
				}
				$where1[]=@implode(" or ",$class);
			}
			$where.=" AND (".@implode(" or ",$where1).")";
		}
		
		if($paramer["job"])
		{
			$where.=" AND `job_post` in ($paramer[job])";
		}
		if($paramer[order] && $paramer[order]!="lastdate")
		{
			$order = " ORDER BY ".str_replace("'","",$paramer[order])."  ";
		}else{
			$order = " ORDER BY `lastupdate` ";
		}
	
		if($paramer[sort])
		{
			$sort = $paramer[sort];
		}else{
			$sort = " DESC";
		}
		if($paramer['orderby']=="rec")
		{
			$nowtime=time();
			$where.=" ORDER BY if(rec_time>$nowtime,rec_time,lastupdate)  desc";
		}else{
			$where.=$order.$sort;
		}
		
		if($paramer[where])
		{
			$where = $paramer[where];
		}
		
		if($paramer[limit])
		{
			$limit = " limit ".$paramer[limit];
		}
		if($paramer[ispage])
		{
			$limit = $this->PageNav($paramer,$_GET,"company_job",$where,$Purl,"","6");
			$this->_tpl_vars["firmurl"] = $config['sy_weburl']."/index.php?m=com".$ParamerArr[firmurl];
		}
		$List = $db->select_all("company_job",$where.$limit);
		
		
		
		if(is_array($List))
		{
			
			$cache_array = $db->cacheget();

			foreach($List as $key=>$value){
				$comuid[] = $value['uid'];
			}
			$comuids = @implode(',',$comuid);
			if($comuids)
			{
				$r_uids=$db->select_all("company","`uid` IN (".$comuids.")","`uid`,`yyzz_status`");
				if(is_array($r_uids))
				{
					foreach($r_uids as $key=>$value){
						$r_uid[$value['uid']] = $value['yyzz_status'];
					}
				}
			}

			foreach($List as $key=>$value)
			{
				$List[$key] = $db->array_action($value,$cache_array);

				$List[$key][stime] = date("Y-m-d",$value[sdate]);
				$List[$key][etime] = date("Y-m-d",$value[edate]);
				$List[$key][lastupdate] = date("Y-m-d",$value[lastupdate]);
				
				$List[$key][yyzz_status] =$r_uid[$value['uid']]['yyzz_status'];
				$time=time()-$value['lastupdate'];

				if($time>86400 && $time<604800){
					$List[$key]['time'] = ceil($time/86400)."天前";
				}elseif($time>3600 && $time<86400){
					$List[$key]['time'] = ceil($time/3600)."小时前";
				}elseif($time>60 && $time<3600){
					$List[$key]['time'] = ceil($time/60)."分钟前";
				}elseif($time<60){
					$List[$key]['time'] = "刚刚";
				}else{
					$List[$key]['time'] = date("Y-m-d",$value['lastupdate']);
				}

				if(is_array($List[$key]['welfare'])&&$List[$key]['welfare']){
					foreach($List[$key]['welfare'] as $val){
						$List[$key]['welfarename'][]=$cache_array['comclass_name'][$val];
					}

				}
			
				if($paramer[comlen])
				{
					$List[$key][com_n] = mb_substr($value['com_name'],0,$paramer[comlen],"GBK");
				}
				
				if($paramer[namelen])
				{
					if($value['rec_time']>time())
					{
						$List[$key][name_n] = "<font color='red'>".mb_substr($value['name'],0,$paramer[namelen],"GBK")."</font>";
					}else{
						$List[$key][name_n] = mb_substr($value['name'],0,$paramer[namelen],"GBK");
					}

				}else{
					if($value['rec_time']>time())
					{
						$List[$key]['name_n'] = "<font color='red'>".$value['name']."</font>";
					}
				}
			
				$List[$key][job_url] = $this->Url("index","com",array("c"=>"comapply","id"=>$value[id]),"1");
				
				$List[$key][com_url] = $this->Curl(array("url"=>"id:".$value[uid]));
				foreach($comrat as $k=>$v)
				{
					if($value[rating]==$v[id])
					{
						$List[$key][color] = $v[com_color];
						$List[$key][ratlogo] = $v[com_pic];
					}
				}
				if($paramer[keyword])
				{
					$List[$key][name]=str_replace($paramer[keyword],"<font color=#FF6600 >".$paramer[keyword]."</font>",$value[name]);
					$List[$key][com_name]=str_replace($paramer[keyword],"<font color=#FF6600 >".$paramer[keyword]."</font>",$value[com_name]);
					$List[$key][name_n]=str_replace($paramer[keyword],"<font color=#FF6600 >".$paramer[keyword]."</font>",$List[$key][name_n]);
					$List[$key][com_n]=str_replace($paramer[keyword],"<font color=#FF6600 >".$paramer[keyword]."</font>",$List[$key][com_n]);
					$List[$key][job_city_one]=str_replace($paramer[keyword],"<font color=#FF6600 >".$paramer[keyword]."</font>",$city_name[$value[provinceid]]);
					$List[$key][job_city_two]=str_replace($paramer[keyword],"<font color=#FF6600 >".$paramer[keyword]."</font>",$city_name[$value[cityid]]);
				}
			}
			if(is_array($List))
			{
				if($paramer[keyword]!=""&&!empty($List))
				{
					$this->addkeywords('3',$paramer[keyword]);
				}
			}

		}
		$this->_tpl_vars[$item] = $List;
		$tag_args = "from=\${$item} " . $tag_args;
		return $this->_compile_foreach_start($tag_args);
	}

	function addkeywords($type,$keyword)
	{
		global $db,$db_config,$config;
	  	$info = $db->DB_select_once("hot_key","`key_name`='$keyword' AND `type`='$type'");
	    if(is_array($info))
	    {
			$db->update_all("hot_key","`num`=`num`+1","`key_name`='$keyword' AND `type`='$type'");
		}else{
		  	$db->insert_once("hot_key","`key_name`='$keyword',`num`='1',`type`='$type',`check`='0'");
	    }
	}
	function PageNav($paramer,$get,$table,$where,$Purl,$table2="",$islt='0')
	{
		
		global $db,$db_config,$config;
			if($paramer['islt'])
			{
				$islt=$paramer['islt'];
			}
			$page=$get[page]<1?1:$get[page];
			if($get['c']){
				$urlarr["c"]=$get['c'];
			}
			$urlarr["page"]="{{page}}";
			if(is_array($Purl))
			{
				foreach($Purl as $key=>$value)
				{
					if($value!="")
					{
						$urlarr[$key] = $value;
					}
				}
			}
			if($islt=="1")
			{
				$lturl=array();
				if(is_array($urlarr))
				{
					foreach($urlarr as $k=>$v)
					{
						$lt_url[]=$k.":".$v;
					}
					$lturl["url"] = @implode(",",$lt_url);
				}
				$pageurl = $this->Lurl($lturl,"1");
			}else if($islt=="2"){

				foreach($urlarr as $k=>$v)
				{
					$ask_url[]=$k.":".$v;
				}
				$askurl["url"] = @implode(",",$ask_url);
				$pageurl = $this->Aurl($askurl,"1");
			}else if($islt=="3"){
				foreach($get as $k=>$v)
				{
					$url[]=$k."=".$v;
				}
				$memberurl=@implode("&",$url);
				$pageurl = $config['sy_weburl']."/member/index.php?".$memberurl."&page={{page}} ";
			}elseif($islt=='4'){
				foreach($get as $k=>$v)
				{
					if($k&&$k!='page')
					{
						$url[]=$k."=".$v;
					}
				}
				$memberurl=@implode("&",$url);
				$pageurl = $config['sy_weburl']."/wap/index.php?".$memberurl."&page={{page}} ";
			}elseif($islt=='5'){
				if($config['sy_news_rewrite']=='2')
				{
					$pageurl = $config['sy_weburl']."/news/".$get['nid']."/{{page}}.html";
				}else{
					$urlarr['page'] = '*page*';
					$pageurl = $this->Url("index","news",$urlarr,"1");
					$pageurl = str_replace('*page*',"{{page}}", $pageurl);
				}

			}elseif($islt=='6'){
				$pageurl = $this->Url("index",$get['m'],$urlarr);
			}else{
				$pageurl = $this->Url("index",$get['m'],$urlarr,"1");
			}
			if($table2)
			{
				$list = $db->select_alls($table,$table2,$where,"count(b.id) as count");
				$count = $list[0][count];
			}else{
				$count = $db->select_num($table,$where);
			}
			$pagesize = $this->Page($page,$count,$paramer[limit],$pageurl,$paramer['notpl']);
			return $limit = " limit $pagesize,$paramer[limit]";
	}
	function Page($pagenum,$num,$limit,$pageurl,$notpl=false)
	{
		global $db,$db_config,$config;
		$path = dirname(dirname(dirname(__FILE__)));
		include($path."/include/page3.class.php");
		$pagenum=$pagenum<1?1:$pagenum;
		$ststrsql=($pagenum-1)*$limit;
		$pages=ceil($num/$limit);
		$page = new page($pagenum,$limit,$num,$pageurl,5,true,$notpl);
		$pagenav=$page->numPage();
		$this->_tpl_vars[limit]=$limit;
		$this->_tpl_vars[pages]=$pages;
		$this->_tpl_vars[total]=$num;
		$this->_tpl_vars[pagenav]=$pagenav;
		return $ststrsql;
	}
	function  Url($con='index',$m='index',$paramer=array(),$index="")
	{
		global $db,$db_config,$config,$seo;
		$paramer['con'] = $con;
		$paramer['m'] = $m;

		$url  =  get_index_url($paramer,$config,$seo,"",$index);
		return $url;
	}
	function  Curl($paramer)
	{
		global $db,$db_config,$config,$seo;
		$url  =  get_url($paramer,$config,$seo,'company');
		return $url;
	}
	function  turl($paramer){
		global $db,$db_config,$config,$seo;
		$url  =  get_url($paramer,$config,$seo,'train');
		return $url;
	}
	function  Lurl($paramer)
	{
		global $db,$db_config,$config,$seo;
		$url  =  get_url($paramer,$config,$seo,'lt');
		return $url;
	}
	function  Aurl($paramer)
	{
		global $db,$db_config,$config,$seo;
		$url  =  get_url($paramer,$config,$seo,'ask');
		return $url;
	}
	function  Furl($paramer)
	{
		global $db,$db_config,$config,$seo;
		$url  =  get_url($paramer,$config,$seo,'friend');
		return $url;
	}
	function GetSmarty($arr,$get)
	{
		$arr = str_replace("\"","",$arr);
		$arr = str_replace("'","",$arr);
		if(is_array($arr))
		{
			foreach($arr as $key=>$value)
			{
				$val = mb_substr($value,0,5);
				if(preg_match ("/auto./i", $value))
				{
					$nval = str_replace("auto.","",$value);
					$purl[$key] = $get[$nval];
					$arr[$key] = $get[$nval];
					if($get[$nval]!="")
					{
						$url.="&".$key."=".$get[$key];
					}
				}
				if(preg_match ("/@./i", $value))
				{
					$nval = str_replace("@","",$value);
					$nval = str_replace("\"","",$nval);
					$nval = @explode(".",$nval);
					if(is_array($nval))
					{
						$smarty_val = $this->_tpl_vars;
						foreach($nval as $k=>$v)
						{
							$smarty_val = $smarty_val[$v];
						}
						$arr[$key] = $smarty_val;
					}
				}
			}
		}
		return array("purl"=>$purl,"arr"=>$arr,"firmurl"=>$url);
	}
	
    function _complie_adlist_start($tag_args)
    {
		$paramer = $this->_parse_attrs($tag_args);
		$item = str_replace("'","",$paramer[item]);
		$path = dirname(dirname(dirname(__FILE__)));
		$class_id = $paramer[classid];
		include($path."/plus/pimg_cache.php");
		if($paramer[adid]){
			if(!empty($ad_label[$class_id]['ad_'.$paramer['adid']])&&($ad_label[$class_id]['ad_'.$paramer[adid]]['did']==$_SESSION['did']||$ad_label[$class_id]['ad_'.$paramer[adid]]['did']=='0')&&$ad_label[$class_id]['ad_'.$paramer[adid]]['start']<time()&&$ad_label[$class_id]['ad_'.$paramer[adid]]['end']>time()){
				$AdArr[] = $ad_label[$class_id]['ad_'.$paramer['adid']];
			}

		}else{
			$add_arr = $ad_label[$class_id];
			if(is_array($add_arr) && !empty($add_arr)){
				$i=0;
				if($paramer[limit] && $paramer[limit]<count($add_arr) && count($add_arr)>0){
					$limit = $paramer[limit];
				}
				if((int)$paramer[length]>0){
					$length = (int)$paramer[length];
				}

				foreach($add_arr as $key=>$value){
				

					if((stripos($value['did'],$_SESSION['did'])!==false ||$value['did']=='0')&&$value['start']<time()&&$value['end']>time()){
						if($i>0 && $limit==$i){
							break;
						}
						if($length>0){
							$value[name] = mb_substr($value[name],0,$length);
						}
						if($paramer[type]!=""){
							if($paramer[type] == $value[type]){
								$AdArr[] = $value;
							}
						}else{
							$AdArr[] = $value;
						}
						$i++;
					}
				}
			}
		}


		$this->_tpl_vars[$item] = $AdArr;
		$tag_args = "from=\${$item} " . $tag_args;
		return $this->_compile_foreach_start($tag_args);
	}
   
    function _compile_capture_tag($start, $tag_args = '')
    {
        $attrs = $this->_parse_attrs($tag_args);
        if ($start)
		{
            $buffer = isset($attrs['name']) ? $attrs['name'] : "'default'";
            $assign = isset($attrs['assign']) ? $attrs['assign'] : null;
            $append = isset($attrs['append']) ? $attrs['append'] : null;
            $output = "<?php ob_start(); ?>";
            $this->_capture_stack[] = array($buffer, $assign, $append);
        } else {
            list($buffer, $assign, $append) = array_pop($this->_capture_stack);
            $output = "<?php \$this->_smarty_vars['capture'][$buffer] = ob_get_contents(); ";
            if (isset($assign)) {
                $output .= " \$this->assign($assign, ob_get_contents());";
            }
            if (isset($append)) {
                $output .= " \$this->append($append, ob_get_contents());";
            }
            $output .= "ob_end_clean(); ?>";
        }
        return $output;
    }
    /**
     * Compile {if ...} tag
     *
     * @param string $tag_args
     * @param boolean $elseif if true, uses elseif instead of if
     * @return string
     */
    function _compile_if_tag($tag_args, $elseif = false)
    {
        /* Tokenize args for 'if' tag. */
        preg_match_all('~(?>
                ' . $this->_obj_call_regexp . '(?:' . $this->_mod_regexp . '*)? | # valid object call
                ' . $this->_var_regexp . '(?:' . $this->_mod_regexp . '*)?    | # var or quoted string
                \-?0[xX][0-9a-fA-F]+|\-?\d+(?:\.\d+)?|\.\d+|!==|===|==|!=|<>|<<|>>|<=|>=|\&\&|\|\||\(|\)|,|\!|\^|=|\&|\~|<|>|\||\%|\+|\-|\/|\*|\@    | # valid non-word token
                \b\w+\b                                                        | # valid word token
                \S+                                                           # anything else
                )~x', $tag_args, $match);
        $tokens = $match[0];
        if(empty($tokens))
		{
            $_error_msg = $elseif ? "'elseif'" : "'if'";
            $_error_msg .= ' statement requires arguments';
            $this->_syntax_error($_error_msg, E_USER_ERROR, __FILE__, __LINE__);
        }
        // make sure we have balanced parenthesis
        $token_count = array_count_values($tokens);
        if(isset($token_count['(']) && $token_count['('] != $token_count[')'])
		{
            $this->_syntax_error("unbalanced parenthesis in if statement", E_USER_ERROR, __FILE__, __LINE__);
        }
        $is_arg_stack = array();
        for ($i = 0; $i < count($tokens); $i++)
		{
            $token = &$tokens[$i];
            switch (strtolower($token))
			{
                case '!':
                case '%':
                case '!==':
                case '==':
                case '===':
                case '>':
                case '<':
                case '!=':
                case '<>':
                case '<<':
                case '>>':
                case '<=':
                case '>=':
                case '&&':
                case '||':
                case '|':
                case '^':
                case '&':
                case '~':
                case ')':
                case ',':
                case '+':
                case '-':
                case '*':
                case '/':
                case '@':
                    break;

                case 'eq':
                    $token = '==';
                    break;

                case 'ne':
                case 'neq':
                    $token = '!=';
                    break;

                case 'lt':
                    $token = '<';
                    break;

                case 'le':
                case 'lte':
                    $token = '<=';
                    break;

                case 'gt':
                    $token = '>';
                    break;

                case 'ge':
                case 'gte':
                    $token = '>=';
                    break;

                case 'and':
                    $token = '&&';
                    break;

                case 'or':
                    $token = '||';
                    break;

                case 'not':
                    $token = '!';
                    break;

                case 'mod':
                    $token = '%';
                    break;

                case '(':
                    array_push($is_arg_stack, $i);
                    break;

                case 'is':
                    /* If last token was a ')', we operate on the parenthesized
                       expression. The start of the expression is on the stack.
                       Otherwise, we operate on the last encountered token. */
                    if ($tokens[$i-1] == ')')
					{
                        $is_arg_start = array_pop($is_arg_stack);
                        if ($is_arg_start != 0)
						{
                            if (preg_match('~^' . $this->_func_regexp . '$~', $tokens[$is_arg_start-1])) {
                                $is_arg_start--;
                            }
                        }
                    } else
                        $is_arg_start = $i-1;
                    /* Construct the argument for 'is' expression, so it knows
                       what to operate on. */
                    $is_arg = @implode(' ', array_slice($tokens, $is_arg_start, $i - $is_arg_start));
                    /* Pass all tokens from next one until the end to the
                       'is' expression parsing function. The function will
                       return modified tokens, where the first one is the result
                       of the 'is' expression and the rest are the tokens it
                       didn't touch. */
                    $new_tokens = $this->_parse_is_expr($is_arg, array_slice($tokens, $i+1));
                    /* Replace the old tokens with the new ones. */
                    array_splice($tokens, $is_arg_start, count($tokens), $new_tokens);
                    /* Adjust argument start so that it won't change from the
                       current position for the next iteration. */
                    $i = $is_arg_start;
                    break;
                default:
                    if(preg_match('~^' . $this->_func_regexp . '$~', $token) )
					{
                            // function call
                            if($this->security &&
                               !in_array($token, $this->security_settings['IF_FUNCS']))
							   {
                                $this->_syntax_error("(secure mode) '$token' not allowed in if statement", E_USER_ERROR, __FILE__, __LINE__);
                            }
                    } elseif(preg_match('~^' . $this->_var_regexp . '$~', $token) && (strpos('+-*/^%&|', substr($token, -1)) === false) && isset($tokens[$i+1]) && $tokens[$i+1] == '(') {
                        // variable function call
                        $this->_syntax_error("variable function call '$token' not allowed in if statement", E_USER_ERROR, __FILE__, __LINE__);
                    } elseif(preg_match('~^' . $this->_obj_call_regexp . '|' . $this->_var_regexp . '(?:' . $this->_mod_regexp . '*)$~', $token)) {
                        // object or variable
                        $token = $this->_parse_var_props($token);
                    } elseif(is_numeric($token)) {
                        // number, skip it
                    } else {
                        $this->_syntax_error("unidentified token '$token'", E_USER_ERROR, __FILE__, __LINE__);
                    }
                    break;
            }
        }
        if ($elseif)
            return '<?php elseif ('.@implode(' ', $tokens).'): ?>';
        else
            return '<?php if ('.@implode(' ', $tokens).'): ?>';
    }
    function _compile_arg_list($type, $name, $attrs, &$cache_code)
	{
        $arg_list = array();
        if (isset($type) && isset($name)
            && isset($this->_plugins[$type])
            && isset($this->_plugins[$type][$name])
            && empty($this->_plugins[$type][$name][4])
            && is_array($this->_plugins[$type][$name][5])
            ) {
            /* we have a list of parameters that should be cached */
            $_cache_attrs = $this->_plugins[$type][$name][5];
            $_count = $this->_cache_attrs_count++;
            $cache_code = "\$_cache_attrs =& \$this->_smarty_cache_attrs('$this->_cache_serial','$_count');";
        } else {
            /* no parameters are cached */
            $_cache_attrs = null;
        }
        foreach ($attrs as $arg_name => $arg_value)
		{
            if (is_bool($arg_value))
                $arg_value = $arg_value ? 'true' : 'false';
            if (is_null($arg_value))
                $arg_value = 'null';
            if ($_cache_attrs && in_array($arg_name, $_cache_attrs)) {
                $arg_list[] = "'$arg_name' => (\$this->_cache_including) ? \$_cache_attrs['$arg_name'] : (\$_cache_attrs['$arg_name']=$arg_value)";
            } else {
                $arg_list[] = "'$arg_name' => $arg_value";
            }
        }
        return $arg_list;
    }
    /**
     * Parse is expression
     *
     * @param string $is_arg
     * @param array $tokens
     * @return array
     */
    function _parse_is_expr($is_arg, $tokens)
    {
        $expr_end = 0;
        $negate_expr = false;
        if (($first_token = array_shift($tokens)) == 'not')
		{
            $negate_expr = true;
            $expr_type = array_shift($tokens);
        } else
            $expr_type = $first_token;
        switch ($expr_type)
		{
            case 'even':
                if (isset($tokens[$expr_end]) && $tokens[$expr_end] == 'by')
				{
                    $expr_end++;
                    $expr_arg = $tokens[$expr_end++];
                    $expr = "!(1 & ($is_arg / " . $this->_parse_var_props($expr_arg) . "))";
                } else
                    $expr = "!(1 & $is_arg)";
                break;

            case 'odd':
                if (isset($tokens[$expr_end]) && $tokens[$expr_end] == 'by')
				{
                    $expr_end++;
                    $expr_arg = $tokens[$expr_end++];
                    $expr = "(1 & ($is_arg / " . $this->_parse_var_props($expr_arg) . "))";
                } else
                    $expr = "(1 & $is_arg)";
                break;

            case 'div':
                if (@$tokens[$expr_end] == 'by')
				{
                    $expr_end++;
                    $expr_arg = $tokens[$expr_end++];
                    $expr = "!($is_arg % " . $this->_parse_var_props($expr_arg) . ")";
                } else {
                    $this->_syntax_error("expecting 'by' after 'div'", E_USER_ERROR, __FILE__, __LINE__);
                }
                break;

            default:
                $this->_syntax_error("unknown 'is' expression - '$expr_type'", E_USER_ERROR, __FILE__, __LINE__);
                break;
        }

        if ($negate_expr)
		{
            $expr = "!($expr)";
        }
        array_splice($tokens, 0, $expr_end, $expr);
        return $tokens;
    }
    /**
     * Parse attribute string
     *
     * @param string $tag_args
     * @return array
     */
    function _parse_attrs($tag_args)
    {
        /* Tokenize tag attributes. */
        preg_match_all('~(?:' . $this->_obj_call_regexp . '|' . $this->_qstr_regexp . ' | (?>[^"\'=\s]+)
                         )+ |
                         [=]
                        ~x', $tag_args, $match);
        $tokens       = $match[0];

        $attrs = array();
        /* Parse state:
            0 - expecting attribute name
            1 - expecting '='
            2 - expecting attribute value (not '=') */
        $state = 0;
        foreach ($tokens as $token)
		{
            switch ($state)
			{
                case 0:
                    /* If the token is a valid identifier, we set attribute name
                       and go to state 1. */
                    if (preg_match('~^\w+$~', $token))
					{
                        $attr_name = $token;
                        $state = 1;
                    } else
                        $this->_syntax_error("invalid attribute name: '$token'", E_USER_ERROR, __FILE__, __LINE__);
                    break;

                case 1:
                    /* If the token is '=', then we go to state 2. */
                    if ($token == '=') {
                        $state = 2;
                    } else
                        $this->_syntax_error("expecting '=' after attribute name '$last_token'", E_USER_ERROR, __FILE__, __LINE__);
                    break;

                case 2:
                    /* If token is not '=', we set the attribute value and go to
                       state 0. */
                    if ($token != '=')
					{
                        /* We booleanize the token if it's a non-quoted possible
                           boolean value. */
                        if (preg_match('~^(on|yes|true)$~', $token))
						{
                            $token = 'true';
                        } else if (preg_match('~^(off|no|false)$~', $token)) {
                            $token = 'false';
                        } else if ($token == 'null') {
                            $token = 'null';
                        } else if (preg_match('~^' . $this->_num_const_regexp . '|0[xX][0-9a-fA-F]+$~', $token)) {
                            /* treat integer literally */
                        } else if (!preg_match('~^' . $this->_obj_call_regexp . '|' . $this->_var_regexp . '(?:' . $this->_mod_regexp . ')*$~', $token)) {
                            /* treat as a string, double-quote it escaping quotes */
                            $token = '"'.addslashes($token).'"';
                        }
                        $attrs[$attr_name] = $token;
                        $state = 0;
                    } else
                        $this->_syntax_error("'=' cannot be an attribute value", E_USER_ERROR, __FILE__, __LINE__);
                    break;
            }
            $last_token = $token;
        }
        if($state != 0)
		{
            if($state == 1)
			{
                $this->_syntax_error("expecting '=' after attribute name '$last_token'", E_USER_ERROR, __FILE__, __LINE__);
            } else {
                $this->_syntax_error("missing attribute value", E_USER_ERROR, __FILE__, __LINE__);
            }
        }
        $this->_parse_vars_props($attrs);
        return $attrs;
    }
    /**
     * compile multiple variables and section properties tokens into
     * PHP code
     *
     * @param array $tokens
     */
    function _parse_vars_props(&$tokens)
    {
        foreach($tokens as $key => $val)
		{
            $tokens[$key] = $this->_parse_var_props($val);
        }
    }
    /**
     * compile single variable and section properties token into
     * PHP code
     *
     * @param string $val
     * @param string $tag_attrs
     * @return string
     */
    function _parse_var_props($val)
    {
        $val = trim($val);
        if(preg_match('~^(' . $this->_obj_call_regexp . '|' . $this->_dvar_regexp . ')(' . $this->_mod_regexp . '*)$~', $val, $match))
		{
            // $ variable or object
            $return = $this->_parse_var($match[1]);
            $modifiers = $match[2];
            if (!empty($this->default_modifiers) && !preg_match('~(^|\|)smarty:nodefaults($|\|)~',$modifiers))
			{
                $_default_mod_string = @implode('|',(array)$this->default_modifiers);
                $modifiers = empty($modifiers) ? $_default_mod_string : $_default_mod_string . '|' . $modifiers;
            }
            $this->_parse_modifiers($return, $modifiers);
            return $return;
        } elseif (preg_match('~^' . $this->_db_qstr_regexp . '(?:' . $this->_mod_regexp . '*)$~', $val)) {
                // double quoted text
                preg_match('~^(' . $this->_db_qstr_regexp . ')('. $this->_mod_regexp . '*)$~', $val, $match);
                $return = $this->_expand_quoted_text($match[1]);
                if($match[2] != '') {
                    $this->_parse_modifiers($return, $match[2]);
                }
                return $return;
            }
        elseif(preg_match('~^' . $this->_num_const_regexp . '(?:' . $this->_mod_regexp . '*)$~', $val)) {
                // numerical constant
                preg_match('~^(' . $this->_num_const_regexp . ')('. $this->_mod_regexp . '*)$~', $val, $match);
                if($match[2] != '') {
                    $this->_parse_modifiers($match[1], $match[2]);
                    return $match[1];
                }
            }
        elseif(preg_match('~^' . $this->_si_qstr_regexp . '(?:' . $this->_mod_regexp . '*)$~', $val)) {
                // single quoted text
                preg_match('~^(' . $this->_si_qstr_regexp . ')('. $this->_mod_regexp . '*)$~', $val, $match);
                if($match[2] != '') {
                    $this->_parse_modifiers($match[1], $match[2]);
                    return $match[1];
                }
            }
        elseif(preg_match('~^' . $this->_cvar_regexp . '(?:' . $this->_mod_regexp . '*)$~', $val)) {
                // config var
                return $this->_parse_conf_var($val);
            }
        elseif(preg_match('~^' . $this->_svar_regexp . '(?:' . $this->_mod_regexp . '*)$~', $val)) {
                // section var
                return $this->_parse_section_prop($val);
            }
        elseif(!in_array($val, $this->_permitted_tokens) && !is_numeric($val)) {
            // literal string
            return $this->_expand_quoted_text('"' . strtr($val, array('\\' => '\\\\', '"' => '\\"')) .'"');
        }
        return $val;
    }
    /**
     * expand quoted text with embedded variables
     *
     * @param string $var_expr
     * @return string
     */
    function _expand_quoted_text($var_expr)
    {
        // if contains unescaped $, expand it
        if(preg_match_all('~(?:\`(?<!\\\\)\$' . $this->_dvar_guts_regexp . '(?:' . $this->_obj_ext_regexp . ')*\`)|(?:(?<!\\\\)\$\w+(\[[a-zA-Z0-9]+\])*)~', $var_expr, $_match)) {
            $_match = $_match[0];
            $_replace = array();
            foreach($_match as $_var) {
                $_replace[$_var] = '".(' . $this->_parse_var(str_replace('`','',$_var)) . ')."';
            }
            $var_expr = strtr($var_expr, $_replace);
            $_return = preg_replace('~\.""|(?<!\\\\)""\.~', '', $var_expr);
        } else {
            $_return = $var_expr;
        }
        // replace double quoted literal string with single quotes
        $_return = preg_replace('~^"([\s\w]+)"$~',"'\\1'",$_return);
        return $_return;
    }
    /**
     * parse variable expression into PHP code
     *
     * @param string $var_expr
     * @param string $output
     * @return string
     */
    function _parse_var($var_expr)
    {
        $_has_math = false;
        $_math_vars = preg_split('~('.$this->_dvar_math_regexp.'|'.$this->_qstr_regexp.')~', $var_expr, -1, PREG_SPLIT_DELIM_CAPTURE);
        if(count($_math_vars) > 1)
		{
            $_first_var = "";
            $_complete_var = "";
            $_output = "";
            // simple check if there is any math, to stop recursion (due to modifiers with "xx % yy" as parameter)
            foreach($_math_vars as $_k => $_math_var)
			{
                $_math_var = $_math_vars[$_k];
                if(!empty($_math_var) || is_numeric($_math_var))
				{
                    // hit a math operator, so process the stuff which came before it
                    if(preg_match('~^' . $this->_dvar_math_regexp . '$~', $_math_var))
					{
                        $_has_math = true;
                        if(!empty($_complete_var) || is_numeric($_complete_var))
						{
                            $_output .= $this->_parse_var($_complete_var);
                        }
                        // just output the math operator to php
                        $_output .= $_math_var;
                        if(empty($_first_var))
                            $_first_var = $_complete_var;
                        $_complete_var = "";
                    } else {
                        $_complete_var .= $_math_var;
                    }
                }
            }
            if($_has_math)
			{
                if(!empty($_complete_var) || is_numeric($_complete_var))
                    $_output .= $this->_parse_var($_complete_var);
                // get the modifiers working (only the last var from math + modifier is left)
                $var_expr = $_complete_var;
            }
        }
        // prevent cutting of first digit in the number (we _definitly_ got a number if the first char is a digit)
        if(is_numeric(substr($var_expr, 0, 1)))
            $_var_ref = $var_expr;
        else
            $_var_ref = substr($var_expr, 1);
        if(!$_has_math)
		{
            // get [foo] and .foo and ->foo and (...) pieces
            preg_match_all('~(?:^\w+)|' . $this->_obj_params_regexp . '|(?:' . $this->_var_bracket_regexp . ')|->\$?\w+|\.\$?\w+|\S+~', $_var_ref, $match);
            $_indexes = $match[0];
            $_var_name = array_shift($_indexes);
            /* Handle $smarty.* variable references as a special case. */
            if ($_var_name == 'smarty')
			{
                /*
                 * If the reference could be compiled, use the compiled output;
                 * otherwise, fall back on the $smarty variable generated at
                 * run-time.
                 */
                if (($smarty_ref = $this->_compile_smarty_ref($_indexes)) !== null)
				{
                    $_output = $smarty_ref;
                } else {
                    $_var_name = substr(array_shift($_indexes), 1);
                    $_output = "\$this->_smarty_vars['$_var_name']";
                }
            } elseif(is_numeric($_var_name) && is_numeric(substr($var_expr, 0, 1))) {
                // because . is the operator for accessing arrays thru inidizes we need to put it together again for floating point numbers
                if(count($_indexes) > 0)
                {
                    $_var_name .= @implode("", $_indexes);
                    $_indexes = array();
                }
                $_output = $_var_name;
            } else {
                $_output = "\$this->_tpl_vars['$_var_name']";
            }
            foreach ($_indexes as $_index)
			{
                if (substr($_index, 0, 1) == '[')
				{
                    $_index = substr($_index, 1, -1);
                    if (is_numeric($_index))
					{
                        $_output .= "[$_index]";
                    } elseif (substr($_index, 0, 1) == '$') {
                        if (strpos($_index, '.') !== false)
						{
                            $_output .= '[' . $this->_parse_var($_index) . ']';
                        } else {
                            $_output .= "[\$this->_tpl_vars['" . substr($_index, 1) . "']]";
                        }
                    } else {
                        $_var_parts = @explode('.', $_index);
                        $_var_section = $_var_parts[0];
                        $_var_section_prop = isset($_var_parts[1]) ? $_var_parts[1] : 'index';
                        $_output .= "[\$this->_sections['$_var_section']['$_var_section_prop']]";
                    }
                } else if (substr($_index, 0, 1) == '.')
				{
                    if (substr($_index, 1, 1) == '$')
                        $_output .= "[\$this->_tpl_vars['" . substr($_index, 2) . "']]";
                    else
                        $_output .= "['" . substr($_index, 1) . "']";
                } else if (substr($_index,0,2) == '->')
				{
                    if(substr($_index,2,2) == '__')
					{
                        $this->_syntax_error('call to internal object members is not allowed', E_USER_ERROR, __FILE__, __LINE__);
                    } elseif($this->security && substr($_index, 2, 1) == '_') {
                        $this->_syntax_error('(secure) call to private object member is not allowed', E_USER_ERROR, __FILE__, __LINE__);
                    } elseif (substr($_index, 2, 1) == '$') {
                        if ($this->security)
						{
                            $this->_syntax_error('(secure) call to dynamic object member is not allowed', E_USER_ERROR, __FILE__, __LINE__);
                        } else {
                            $_output .= '->{(($_var=$this->_tpl_vars[\''.substr($_index,3).'\']) && substr($_var,0,2)!=\'__\') ? $_var : $this->trigger_error("cannot access property \\"$_var\\"")}';
                        }
                    } else {
                        $_output .= $_index;
                    }
                } elseif (substr($_index, 0, 1) == '(') {
                    $_index = $this->_parse_parenth_args($_index);
                    $_output .= $_index;
                } else {
                    $_output .= $_index;
                }
            }
        }
        return $_output;
    }
    /**
     * parse arguments in function call parenthesis
     *
     * @param string $parenth_args
     * @return string
     */
    function _parse_parenth_args($parenth_args)
    {
        preg_match_all('~' . $this->_param_regexp . '~',$parenth_args, $match);
        $orig_vals = $match = $match[0];
        $this->_parse_vars_props($match);
        $replace = array();
        for ($i = 0, $count = count($match); $i < $count; $i++)
		{
            $replace[$orig_vals[$i]] = $match[$i];
        }
        return strtr($parenth_args, $replace);
    }
    /**
     * parse configuration variable expression into PHP code
     *
     * @param string $conf_var_expr
     */
    function _parse_conf_var($conf_var_expr)
    {
        $parts = @explode('|', $conf_var_expr, 2);
        $var_ref = $parts[0];
        $modifiers = isset($parts[1]) ? $parts[1] : '';
        $var_name = substr($var_ref, 1, -1);
        $output = "\$this->_config[0]['vars']['$var_name']";
        $this->_parse_modifiers($output, $modifiers);
        return $output;
    }
    /**
     * parse section property expression into PHP code
     *
     * @param string $section_prop_expr
     * @return string
     */
    function _parse_section_prop($section_prop_expr)
    {
        $parts = @explode('|', $section_prop_expr, 2);
        $var_ref = $parts[0];
        $modifiers = isset($parts[1]) ? $parts[1] : '';
        preg_match('!%(\w+)\.(\w+)%!', $var_ref, $match);
        $section_name = $match[1];
        $prop_name = $match[2];
        $output = "\$this->_sections['$section_name']['$prop_name']";
        $this->_parse_modifiers($output, $modifiers);
        return $output;
    }
    /**
     * parse modifier chain into PHP code
     *
     * sets $output to parsed modified chain
     * @param string $output
     * @param string $modifier_string
     */
    function _parse_modifiers(&$output, $modifier_string)
    {
        preg_match_all('~\|(@?\w+)((?>:(?:'. $this->_qstr_regexp . '|[^|]+))*)~', '|' . $modifier_string, $_match);
        list(, $_modifiers, $modifier_arg_strings) = $_match;
        for ($_i = 0, $_for_max = count($_modifiers); $_i < $_for_max; $_i++)
		{
            $_modifier_name = $_modifiers[$_i];
            if($_modifier_name == 'smarty')
			{
                // skip smarty modifier
                continue;
            }
            preg_match_all('~:(' . $this->_qstr_regexp . '|[^:]+)~', $modifier_arg_strings[$_i], $_match);
            $_modifier_args = $_match[1];
            if (substr($_modifier_name, 0, 1) == '@')
			{
                $_map_array = false;
                $_modifier_name = substr($_modifier_name, 1);
            } else {
                $_map_array = true;
            }
            if (empty($this->_plugins['modifier'][$_modifier_name])
                && !$this->_get_plugin_filepath('modifier', $_modifier_name)
                && function_exists($_modifier_name)) {
                if ($this->security && !in_array($_modifier_name, $this->security_settings['MODIFIER_FUNCS'])) {
                    $this->_trigger_fatal_error("[plugin] (secure mode) modifier '$_modifier_name' is not allowed" , $this->_current_file, $this->_current_line_no, __FILE__, __LINE__);
                } else {
                    $this->_plugins['modifier'][$_modifier_name] = array($_modifier_name,  null, null, false);
                }
            }
            $this->_add_plugin('modifier', $_modifier_name);
            $this->_parse_vars_props($_modifier_args);
            if($_modifier_name == 'default')
			{
                // supress notifications of default modifier vars and args
                if(substr($output, 0, 1) == '$')
				{
                    $output = '@' . $output;
                }
                if(isset($_modifier_args[0]) && substr($_modifier_args[0], 0, 1) == '$')
				{
                    $_modifier_args[0] = '@' . $_modifier_args[0];
                }
            }
            if (count($_modifier_args) > 0)
                $_modifier_args = ', '.@implode(', ', $_modifier_args);
            else
                $_modifier_args = '';
            if ($_map_array)
			{
                $output = "((is_array(\$_tmp=$output)) ? \$this->_run_mod_handler('$_modifier_name', true, \$_tmp$_modifier_args) : " . $this->_compile_plugin_call('modifier', $_modifier_name) . "(\$_tmp$_modifier_args))";
            } else {
                $output = $this->_compile_plugin_call('modifier', $_modifier_name)."($output$_modifier_args)";
            }
        }
    }
    /**
     * add plugin
     *
     * @param string $type
     * @param string $name
     * @param boolean? $delayed_loading
     */
    function _add_plugin($type, $name, $delayed_loading = null)
    {
        if (!isset($this->_plugin_info[$type]))
		{
            $this->_plugin_info[$type] = array();
        }
        if (!isset($this->_plugin_info[$type][$name]))
		{
            $this->_plugin_info[$type][$name] = array($this->_current_file,
                                                      $this->_current_line_no,
                                                      $delayed_loading);
        }
    }
    /**
     * Compiles references of type $smarty.foo
     *
     * @param string $indexes
     * @return string
     */
    function _compile_smarty_ref(&$indexes)
    {
        /* Extract the reference name. */
        $_ref = substr($indexes[0], 1);
        foreach($indexes as $_index_no=>$_index)
		{
            if (substr($_index, 0, 1) != '.' && $_index_no<2 || !preg_match('~^(\.|\[|->)~', $_index))
			{
                $this->_syntax_error('$smarty' . @implode('', array_slice($indexes, 0, 2)) . ' is an invalid reference', E_USER_ERROR, __FILE__, __LINE__);
            }
        }
        switch ($_ref)
		{
            case 'now':
                $compiled_ref = 'time()';
                $_max_index = 1;
                break;

            case 'foreach':
                array_shift($indexes);
                $_var = $this->_parse_var_props(substr($indexes[0], 1));
                $_propname = substr($indexes[1], 1);
                $_max_index = 1;
                switch ($_propname) {
                    case 'index':
                        array_shift($indexes);
                        $compiled_ref = "(\$this->_foreach[$_var]['iteration']-1)";
                        break;

                    case 'first':
                        array_shift($indexes);
                        $compiled_ref = "(\$this->_foreach[$_var]['iteration'] <= 1)";
                        break;

                    case 'last':
                        array_shift($indexes);
                        $compiled_ref = "(\$this->_foreach[$_var]['iteration'] == \$this->_foreach[$_var]['total'])";
                        break;

                    case 'show':
                        array_shift($indexes);
                        $compiled_ref = "(\$this->_foreach[$_var]['total'] > 0)";
                        break;

                    default:
                        unset($_max_index);
                        $compiled_ref = "\$this->_foreach[$_var]";
                }
                break;

            case 'section':
                array_shift($indexes);
                $_var = $this->_parse_var_props(substr($indexes[0], 1));
                $compiled_ref = "\$this->_sections[$_var]";
                break;

            case 'get':
                if ($this->security && !$this->security_settings['ALLOW_SUPER_GLOBALS']) {
                    $this->_syntax_error("(secure mode) super global access not permitted",
                                         E_USER_WARNING, __FILE__, __LINE__);
                    return;
                }
                $compiled_ref = "\$_GET";
                break;

            case 'post':
                if ($this->security && !$this->security_settings['ALLOW_SUPER_GLOBALS']) {
                    $this->_syntax_error("(secure mode) super global access not permitted",
                                         E_USER_WARNING, __FILE__, __LINE__);
                    return;
                }
                $compiled_ref = "\$_POST";
                break;

            case 'cookies':
                if ($this->security && !$this->security_settings['ALLOW_SUPER_GLOBALS']) {
                    $this->_syntax_error("(secure mode) super global access not permitted",
                                         E_USER_WARNING, __FILE__, __LINE__);
                    return;
                }
                $compiled_ref = "\$_COOKIE";
                break;

            case 'env':
                if ($this->security && !$this->security_settings['ALLOW_SUPER_GLOBALS']) {
                    $this->_syntax_error("(secure mode) super global access not permitted",
                                         E_USER_WARNING, __FILE__, __LINE__);
                    return;
                }
                $compiled_ref = "\$_ENV";
                break;

            case 'server':
                if ($this->security && !$this->security_settings['ALLOW_SUPER_GLOBALS']) {
                    $this->_syntax_error("(secure mode) super global access not permitted",
                                         E_USER_WARNING, __FILE__, __LINE__);
                    return;
                }
                $compiled_ref = "\$_SERVER";
                break;

            case 'session':
                if ($this->security && !$this->security_settings['ALLOW_SUPER_GLOBALS']) {
                    $this->_syntax_error("(secure mode) super global access not permitted",
                                         E_USER_WARNING, __FILE__, __LINE__);
                    return;
                }
                $compiled_ref = "\$_SESSION";
                break;

            /*
             * These cases are handled either at run-time or elsewhere in the
             * compiler.
             */
            case 'request':
                if ($this->security && !$this->security_settings['ALLOW_SUPER_GLOBALS'])
				{
                    $this->_syntax_error("(secure mode) super global access not permitted",
                                         E_USER_WARNING, __FILE__, __LINE__);
                    return;
                }
                if ($this->request_use_auto_globals)
				{
                    $compiled_ref = "\$_REQUEST";
                    break;
                } else {
                    $this->_init_smarty_vars = true;
                }
                return null;

            case 'capture':
                return null;

            case 'template':
                $compiled_ref = "'$this->_current_file'";
                $_max_index = 1;
                break;

            case 'version':
                $compiled_ref = "'$this->_version'";
                $_max_index = 1;
                break;

            case 'const':
                if ($this->security && !$this->security_settings['ALLOW_CONSTANTS']) {
                    $this->_syntax_error("(secure mode) constants not permitted",
                                         E_USER_WARNING, __FILE__, __LINE__);
                    return;
                }
                array_shift($indexes);
                if (preg_match('!^\.\w+$!', $indexes[0])) {
                    $compiled_ref = '@' . substr($indexes[0], 1);
                } else {
                    $_val = $this->_parse_var_props(substr($indexes[0], 1));
                    $compiled_ref = '@constant(' . $_val . ')';
                }
                $_max_index = 1;
                break;

            case 'config':
                $compiled_ref = "\$this->_config[0]['vars']";
                $_max_index = 3;
                break;

            case 'ldelim':
                $compiled_ref = "'$this->left_delimiter'";
                break;

            case 'rdelim':
                $compiled_ref = "'$this->right_delimiter'";
                break;

            default:
                $this->_syntax_error('$smarty.' . $_ref . ' is an unknown reference', E_USER_ERROR, __FILE__, __LINE__);
                break;
        }

        if (isset($_max_index) && count($indexes) > $_max_index)
		{
            $this->_syntax_error('$smarty' . @implode('', $indexes) .' is an invalid reference', E_USER_ERROR, __FILE__, __LINE__);
        }
        array_shift($indexes);
        return $compiled_ref;
    }
    /**
     * compiles call to plugin of type $type with name $name
     * returns a string containing the function-name or method call
     * without the paramter-list that would have follow to make the
     * call valid php-syntax
     *
     * @param string $type
     * @param string $name
     * @return string
     */
    function _compile_plugin_call($type, $name)
	{
        if (isset($this->_plugins[$type][$name]))
		{
            /* plugin loaded */
            if (is_array($this->_plugins[$type][$name][0]))
			{
                return ((is_object($this->_plugins[$type][$name][0][0])) ?
                        "\$this->_plugins['$type']['$name'][0][0]->"    /* method callback */
                        : (string)($this->_plugins[$type][$name][0][0]).'::'    /* class callback */
                       ). $this->_plugins[$type][$name][0][1];

            } else {
                /* function callback */
                return $this->_plugins[$type][$name][0];
            }
        } else {
            /* plugin not loaded -> auto-loadable-plugin */
            return 'smarty_'.$type.'_'.$name;
        }
    }
    /**
     * load pre- and post-filters
     */
    function _load_filters()
    {
        if (count($this->_plugins['prefilter']) > 0)
		{
            foreach ($this->_plugins['prefilter'] as $filter_name => $prefilter)
			{
                if ($prefilter === false)
				{
                    unset($this->_plugins['prefilter'][$filter_name]);
                    $_params = array('plugins' => array(array('prefilter', $filter_name, null, null, false)));
                    require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
                    smarty_core_load_plugins($_params, $this);
                }
            }
        }
        if (count($this->_plugins['postfilter']) > 0)
		{
            foreach ($this->_plugins['postfilter'] as $filter_name => $postfilter)
			{
                if ($postfilter === false)
				{
                    unset($this->_plugins['postfilter'][$filter_name]);
                    $_params = array('plugins' => array(array('postfilter', $filter_name, null, null, false)));
                    require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
                    smarty_core_load_plugins($_params, $this);
                }
            }
        }
    }
    /**
     * Quote subpattern references
     *
     * @param string $string
     * @return string
     */
    function _quote_replace($string)
    {
        return strtr($string, array('\\' => '\\\\', '$' => '\\$'));
    }
    /**
     * display Smarty syntax error
     *
     * @param string $error_msg
     * @param integer $error_type
     * @param string $file
     * @param integer $line
     */
    function _syntax_error($error_msg, $error_type = E_USER_ERROR, $file=null, $line=null)
    {
        $this->_trigger_fatal_error("syntax error: $error_msg", $this->_current_file, $this->_current_line_no, $file, $line, $error_type);
    }
    /**
     * check if the compilation changes from cacheable to
     * non-cacheable state with the beginning of the current
     * plugin. return php-code to reflect the transition.
     * @return string
     */
    function _push_cacheable_state($type, $name)
	{
        $_cacheable = !isset($this->_plugins[$type][$name]) || $this->_plugins[$type][$name][4];
        if ($_cacheable
            || 0<$this->_cacheable_state++) return '';
        if (!isset($this->_cache_serial)) $this->_cache_serial = md5(uniqid('Smarty'));
        $_ret = 'if ($this->caching && !$this->_cache_including): echo \'{nocache:'
            . $this->_cache_serial . '#' . $this->_nocache_count
            . '}\'; endif;';
        return $_ret;
    }
    /**
     * check if the compilation changes from non-cacheable to
     * cacheable state with the end of the current plugin return
     * php-code to reflect the transition.
     * @return string
     */
    function _pop_cacheable_state($type, $name)
	{
        $_cacheable = !isset($this->_plugins[$type][$name]) || $this->_plugins[$type][$name][4];
        if ($_cacheable
            || --$this->_cacheable_state>0) return '';
        return 'if ($this->caching && !$this->_cache_including): echo \'{/nocache:'
            . $this->_cache_serial . '#' . ($this->_nocache_count++)
            . '}\'; endif;';
    }
    /**
     * push opening tag-name, file-name and line-number on the tag-stack
     * @param string the opening tag's name
     */
    function _push_tag($open_tag)
    {
        array_push($this->_tag_stack, array($open_tag, $this->_current_line_no));
    }
    /**
     * pop closing tag-name
     * raise an error if this stack-top doesn't match with the closing tag
     * @param string the closing tag's name
     * @return string the opening tag's name
     */
    function _pop_tag($close_tag)
    {
        $message = '';
        if (count($this->_tag_stack)>0)
		{
            list($_open_tag, $_line_no) = array_pop($this->_tag_stack);
            if ($close_tag == $_open_tag)
			{
                return $_open_tag;
            }
            if ($close_tag == 'if' && ($_open_tag == 'else' || $_open_tag == 'elseif' ))
			{
                return $this->_pop_tag($close_tag);
            }
            if ($close_tag == 'section' && $_open_tag == 'sectionelse')
			{
                $this->_pop_tag($close_tag);
                return $_open_tag;
            }
            if ($close_tag == 'foreach' && $_open_tag == 'foreachelse')
			{
                $this->_pop_tag($close_tag);
                return $_open_tag;
            }
            if ($_open_tag == 'else' || $_open_tag == 'elseif') {
                $_open_tag = 'if';
            } elseif ($_open_tag == 'sectionelse') {
                $_open_tag = 'section';
            } elseif ($_open_tag == 'foreachelse') {
                $_open_tag = 'foreach';
            }
            $message = " expected {/$_open_tag} (opened line $_line_no).";
        }
        $this->_syntax_error("mismatched tag {/$close_tag}.$message",
                             E_USER_ERROR, __FILE__, __LINE__);
    }
	/*
		2013-7-15	问答模块 列出问答
	*/
	function _complie_qlist_start($tag_args)
	{
		$paramer = $this->_parse_attrs($tag_args);
		$item = str_replace("'","",$paramer[item]);
		global $db,$db_config,$config;
		$path = dirname(dirname(dirname(__FILE__)));
		$ParamerArr = $this->GetSmarty($paramer,$_GET);
		$paramer = $ParamerArr[arr];
		$Purl =  $ParamerArr[purl];

		$where=1;
		//排序字段默认为更新时间
		if($paramer[order]){
			if($paramer[order]=="addtime"){
				$paramer[order]="add_time";
			}
			if($paramer[order]=="answernum"){
				$paramer[order]="answer_num";
			}
			$order = " ORDER BY `".$paramer[order]."`  desc";
		}else{
			$order = " ORDER BY `add_time` desc";
		}
		if($paramer[cid]){
			$where .=" and `cid`='".$paramer[cid]."'";
		}
		if($paramer[uid]){
			$where .=" and `uid`='".$_COOKIE[uid]."'";
		}
		if($paramer[recom]){//推荐 字段
			$where .=" and `is_recom`='1'";
		}
		if($paramer[limit]){
			$limit=" limit ".$paramer[limit];
		}
		if($paramer[ispage]){
			$limit = $this->PageNav($paramer,$_GET,"question",$where,$Purl,"","2");
			//$limit = $this->PageNav($paramer,$_GET,"q_class",$where,$Purl,'','2');
			//$this->_tpl_vars["firmurl"] = $config['sy_weburl']."/index.php?m=question".$ParamerArr[firmurl];
		}
		$rs = $db->select_all("question",$where.$order.$limit);

		foreach($rs as $key=>$val){
			if(intval($paramer[t_len])>0){
				$len = intval($paramer[t_len]);
				$val[title] = mb_substr($val[title],0,$len,"GBK");
			}
			$rs[$key][url] = $this->Aurl(array("url"=>"c:content,id:".$val[id]));
			$ListId[] =  $val[uid];
			$Qclass[]=$val[cid];//问题类别
		}
		//获得提问者uid，并根据uid 获得头像、昵称
		$uids=@implode(",",$ListId);
		$friend_info=$db->select_all("friend_info","`uid` in (".$uids.")","`uid`,`nickname`,`pic`,`description`");
		$atn=$db->select_all("atn","`uid`='".$_COOKIE['uid']."'","`sc_uid`");

		foreach($rs as $r_k=>$r_v){
			foreach($friend_info as $f_v){
				if($r_v['uid']==$f_v['uid']){
					if($f_v['pic']){
						$rs[$r_k]['pic']=str_replace("..",$config["sy_weburl"],$f_v['pic']);
					}else{
						$rs[$r_k]['pic']=$config["sy_weburl"]."/".$config['sy_friend_icon'];
					}
					$rs[$r_k]['uid']=$f_v['uid'];
					$rs[$r_k]['nickname']=$f_v['nickname'];
					$rs[$r_k]['description']=$f_v['description'];
				}
			}
			if($r_v['uid']==$_COOKIE['uid']){
				$rs[$r_k]['is_atn']='2';//表示这是本人，不显示关注按钮
			}
			foreach($atn as $a_v){
				if($a_v['sc_uid']==$r_v['uid']){
					$rs[$r_k]['is_atn']='1';//表示已经关注用户
				}
			}
		}
		$this->_tpl_vars[$item] = $rs;
		$tag_args = "from=\${$item} " . $tag_args;
		return $this->_compile_foreach_start($tag_args);
	}
	function _complie_qrecom_start($tag_args)
	{
		$paramer = $this->_parse_attrs($tag_args);
		$item = str_replace("'","",$paramer[item]);
		global $db,$db_config,$config;
		$path = dirname(dirname(dirname(__FILE__)));
		$ParamerArr = $this->GetSmarty($paramer,$_GET);
		$paramer = $ParamerArr[arr];
		$Purl =  $ParamerArr[purl];
		$atn=$db->select_all("atn","`uid`='".$_COOKIE['uid']."'","`sc_uid`");
		foreach($atn as $a_v)
		{
			$atn_uid.=$a_v['sc_uid'].',';//我已近关注过的用户id
		}
		$atn_uid =$atn_uid.$_COOKIE['uid'];
		$attention=$db->select_all("attention","`type`='2' and `uid` not in(".$atn_uid.") order by rand() limit 10","`uid`,`ids`");
		foreach($attention as $a_k=>$a_v)
		{
			$uid[]=$a_v['uid'];
			$class_id.=$a_v['ids'];
		}
		$uids=@implode(',',$uid);
		$class_ids=@implode(',',array_unique(@explode(',',rtrim($class_id,','))));
		$q_class = $db->select_all("q_class","id in(".$class_ids.")","`id`,`name`");
		$member = $db->select_all("friend_info","uid in(".$uids.") and `nickname`<>''","`uid`,`nickname`,`pic`,`description`");
		foreach($attention as $key=>$val)
		{
			$cid=@explode(',',rtrim($val['ids'],','));
			if($val['uid']==$_COOKIE['uid'])
			{
				$attention[$key]['is_atn']='2';//表示这是本人，不显示关注按钮
			}
			foreach($q_class as $q_v)
			{
				if(in_array($q_v['id'],$cid))
				{
					$class_name[]=$q_v['name'];
				}
			}
			foreach($member as $m_val)
			{
				if($val['uid']==$m_val['uid'])
				{
					$attention[$key]['nickname']=$m_val['nickname'];
					if($m_val['pic'])
					{
						$attention[$key]['pic']=str_replace("..",$config['sy_weburl'],$m_val['pic']);
					}else{
						$attention[$key]['pic']=$config['sy_weburl']."/".$config['sy_friend_icon'];
					}
					$attention[$key]['description']=$m_val['description'];
				}
			}
			if($class_name)
			{
				$attention[$key]['class_name']=@implode('、',$class_name);
			}
			unset($class_name);
			unset($cid);
		}
		if(empty($attention))
		{
			$attention="";
		}
		$this->_tpl_vars[$item] = $attention;
		$tag_args = "from=\${$item} " . $tag_args;
		return $this->_compile_foreach_start($tag_args);
	}
	function _complie_mlist_start($tag_args)
	{
		$paramer = $this->_parse_attrs($tag_args);
		$item = str_replace("'","",$paramer[item]);
		global $db,$db_config,$config;
		$path = dirname(dirname(dirname(__FILE__)));
		$ParamerArr = $this->GetSmarty($paramer,$_GET);
		$paramer = $ParamerArr[arr];
		$Purl =  $ParamerArr[purl];
		$where='1';
		//添加用户类别条件
		if($paramer[usertype])
		{
			$where .= " and `usertype`='".$paramer[usertype]."'";
		}
		//添加状态条件
		if($paramer[status])
		{
			$where .= " and `status`='".$paramer[status]."'";
		}
		if($paramer[ispage])
		{
			$limit = $this->PageNav($paramer,$_GET,"q_class",$where,$Purl,'','2');
		}
		if($paramer[order])
		{
			$order = " ORDER BY `".$paramer[order]."`  desc";
		}else{
			$order = " ORDER BY `uid` desc";
		}
		if($paramer[limit])
		{
			$limit=" limit ".$paramer[limit];
		}
		$member = $db->select_all("member",$where.$order.$limit);
		if(is_array($member))
		{
			foreach($member as $m_k=>$m_v)
			{
				$member[$m_k]['url']=$this->Furl(array("url"=>"c:profile,id:".$m_v['uid']));

			}
		}
		$this->_tpl_vars[$item] = $member;
		$tag_args = "from=\${$item} " . $tag_args;
		return $this->_compile_foreach_start($tag_args);
	}

}
/**
 * compare to values by their string length
 *
 * @access private
 * @param string $a
 * @param string $b
 * @return 0|-1|1
 */
function _smarty_sort_length($a, $b)
{
    if($a == $b)
        return 0;
    if(strlen($a) == strlen($b))
        return ($a > $b) ? -1 : 1;
    return (strlen($a) > strlen($b)) ? -1 : 1;
}