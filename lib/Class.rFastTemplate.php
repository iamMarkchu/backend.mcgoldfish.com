<?php 

if (!defined("CLASS_RFASTTEMPLATE_PHP")){
   define("CLASS_RFASTTEMPLATE_PHP","Y");
   //ini_set("display_errors", "Off");
   error_reporting(E_ALL ^ E_NOTICE);
//
//
//
// Synopsis
//
//    require ("PATH-TO-TEMPLATE-CODE/class.Template.php");
//    $t = new Template("PATH-TO-TEMPLATE-DIRECTORY");
//    $t->define (array(MAIN => "diary.html"));
//    $t->setkey (VAR1, "some text");
//    $t->subst (INNER, "inner")
//    $t->setkey (VAR1, "some more text");
//    $t->subst (INNER, ".inner")
//    $t->setkey (VAR2, "var2 text");
//    $t->subst (CONTENT, "main");
//    $t->print (CONTENT);
//
//

class rFastTemplate {

   // File name to be used for debugging output.  Needs to be set prior to
   // calling anything other than option setting commands (debug, debugall,
   // strict, dynamic) because once the file has been opened, this is ignored.
   //var $DEBUGFILE = '/tmp/class.rFastTemplate.php.dbg';
   var $DEBUGFILE = '';

   // File descriptor for debugging output.
   var $DEBUGFD = -1;

   // Array for individual member functions.  You can turn on debugging for a
   // particular member function by calling $this->debug(FUNCTION_NAME)
   var $DEBUG = array ();

   // Turn this on to turn on debugging in all member functions via
   // $this->debugall().  Turn if off via $this->debugall(false);
   var $DEBUGALL = false;

   // Names of actual templates.  Each element will be an array with template
   // information including is originating file, file load status, parent
   // template, variable list, and actual template contents.
   var $TEMPLATE = array();

   //  Holds paths-to-templates (See: set_root and FindTemplate)
   var $ROOT     = array();

   //  Holds the HANDLE to the last template parsed by parse()
   var $LAST     = '';


   // Strict template checking.  Unresolved variables in templates will generate a
   // warning.
   var $STRICT   = true;

   // Holds handles assigned by a call to parse().
   var $HANDLE   = array();

   // Holds all assigned variable names and values.
   var $VAR      = array();

   // Set to true is this is a WIN32 server.  This was part of the
   // class.FastTemplate.php3 implementation and the only real place it kicks
   // in is in setting the terminating character on the value of $ROOT, the
   // path where all the templates live.
   var $WIN32    = false;

   // Automatically scan template for dynamic templates and assign new values
   // to TEMPLATE based on whatever names the HTML comments use.  This can be
   // changed up until the time the first parse() is called.  Well, you can
   // change it anytime, but it will have no effect on already loaded
   // templates.  Also, if you have dynamic templates, the first call to parse
   // will load ALL of your templates, so changing it after that point will
   // have no effect on any defined templates.
   var $DYNAMIC   = true;

   // Grrr.  Don't try to break these extra long regular expressions into
   // multiple lines for readability.  PHP 4.03pl1 chokes on them if you do.
   // I'm guessing the reason is something obscure with the parenthesis
   // matching, the same sort of thing Tcl might have, but I'm not sure.

   // Regular expression which matches the beginning of a dynamic/inferior
   // template.  The critical bit is that we need two parts: (1) the entire
   // match, and (2) the name of the dynamic template.  The first part is
   // required because will do a strstr() to split the buffer into two
   // pieces: everything before the dynamic template declaration and
   // everything after.  The second is needed because after finding a BEGIN
   // we will search for an END and they both have to have the same name of
   // we consider the template malformed and throw and error.

   // Both of these are written with PCRE (Perl-Compatible Regular
   // Expressions) because we need the non-greedy operators to insure that
   // we don't read past the end of the HTML comment marker in the case that
   // the BEGIN/END block have trailing comments after the tag name.
   var $REGEX_DYNBEG = '/(<!--\s*BEGIN\s+DYNAMIC\s+BLOCK:\s*([A-Za-z][-_A-Za-z0-9.]+)(\s*|\s+.*?)-->)/';

   // Regular expression which matches the end of a dynamic/inferior
   // template; see the comment about on the BEGIN match.
   var $REGEX_DYNEND = '/(<!--\s*END\s+DYNAMIC\s+BLOCK:\s*([A-Za-z][-_A-Za-z0-9.]+)(\s*|\s+.*?)-->)/';
   // Regular expression which matches a variable in the template.

   var $REGEX_VAR_ARRAY = array('standard' => '/\{[A-Za-z][-_A-Za-z0-9]*\}/', 'vals' => '/\%{2}[A-Za-z][-_A-Za-z0-9]*\%{2}/');
   var $REGEX_VAR       = '/\{[A-Za-z][-_A-Za-z0-9]*\}/';

   var $VerifyUnmatched = "yes";
   //
   // Description
   //    Constructor.
   //
   function rFastTemplate ($pathToTemplates = '') {

      // $pathToTemplates can also be an array of template roots, handled in set_root
      global $php_errormsg;
      if (!empty($pathToTemplates)) {
         $this->set_root ($pathToTemplates);
      }
      return $this;
   }

   //
   // Description
   //    Set the name to be used for debugging output.  If another file has
   //    already been opened, close it so the next call to logwrite will
   //    reopen under this name.
   //
   function debugfile ($name) {
      $this->DEBUGFILE = $name;
   }

   //
   // Description
   //    Turn on/off debugging output of an individual member function.
   //
   function debug ($what, $on = true) {
      $this->DEBUG[$what] = $on;
   }

   //
   // Description
   //    Turn on/off debugging output of all member functions.
   //
   function debugall ($on = true) {
      $this->DEBUGALL = $on;
   }

   //
   // Description
   //    Turn on/off automatic dynamic template expansion.  Note that a
   //    template with an inferior dynamic template embedded will still
   //    parse but only as if it were part of the main template.  When this
   //    is turned on, it will be parsed out as as if it were a full-blown
   //    template and can thus be both parsed and appended to as a separate
   //    entity.
   //
   function dynamic ($on = true) {
      $this->DYNAMIC = $on;
   }

   //
   // Description
   //    Turn on/off strict template checking.  When on, all template tags
   //    must be assigned or we throw an error (but stilll parse the
   //    template).
   //
   function strict ($on = true) {
      $this->STRICT = $on;
   }

   //
   // Description
   //    For compatibility with class.FastTemplate.php3.
   //
   function no_strict () {
      $this->STRICT = false;
   }

   //
   // Description
   //    Utility function for debugging.
   //
   function logwrite ($msg) {
      if ($this->DEBUGFD < 0) {
         $this->DEBUGFD = fopen ($this->DEBUGFILE, 'a');
      }
      fputs ($this->DEBUGFD,
             strftime ('%Y/%m/%d %H:%M:%S ') . $msg . "\n");
   }

   //
   // Description
   //    This was lifted as-is from class.FastTemplate.php3.  Based on what
   //    platform is in use, it makes sure the path specification ends with
   //    the proper path separator; i.e., a slash on unix systems and a
   //    back-slash on WIN32 systems.  When we can run on Mac or VMS I guess
   //    we'll worry about other characters....
   //
   //    $root can now be an array of template roots which will be searched to
   //    find the first matching name.
   function set_root ($root) {
      if (!is_array($root)) {
	 $trailer = substr ($root, -1);
	 if ($trailer != ($this->WIN32 ? '\\' : '/'))
	    $root .= ($this->WIN32 ? '\\' : '/');

	 if (!is_dir($root)) {
	    $this->error ("Specified ROOT dir [$root] is not a directory", true);
	    return false;
         }
	 $this->ROOT[] = $root;
      } else {
	 reset($root);
	 while(list($k, $v) = each($root)) {
	    if (is_dir($v)) {
	       $trailer = substr ($v,-1);
	       if ($trailer != ($this->WIN32 ? '\\' : '/'))
		  $v .= ($this->WIN32 ? '\\' : '/');
	       $this->ROOT[] = $v;
	    } else
	       $this->error ("Specified ROOT dir [$v] is not a directory", true);
         }
      }
   }

   //
   // Description
   //    Associate files with a template names.
   //
   // Sigh.  At least with the CVS version of PHP, $dynamic = false sets it
   // to true.
   //
   function define ($fileList, $dynamic = 0) {
      reset ($fileList);
      while (list ($tpl, $file) = each ($fileList)) {
         $this->TEMPLATE[$tpl] = array ('file' => $file, 'dynamic' => $dynamic);
      }
      return true;
   }

   function define_dynamic ($tplList, $parent='') {
      if (is_array($tplList)) {
		 reset ($tplList);
		 while (list ($tpl, $parent) = each ($tplList)) {
			$this->TEMPLATE[$tpl]['parent'] = $parent;
			$this->TEMPLATE[$tpl]['dynamic'] = true;
		 }
      } else {
	 // $tplList is not an array, but a single child/parent pair.
	 $this->TEMPLATE[$tplList]['parent'] = $parent;
	 $this->TEMPLATE[$tplList]['dynamic'] = true;
      }
   }

   //
   // Description
   //     Try each directory in our list of possible roots in turn until we find a matching template
   //
   function FindTemplate ($file) {
      // first try for a template in the current directory short path for
      // absolute filenames
      if (substr($file, 0, 1) == '/') {
         if (file_exists($file)) {
            return $file;
         }
      }

      // search path for a matching file
      reset($this->ROOT);
      while(list($k, $v) = each($this->ROOT)) {
         $f = $v . $file;
         if (file_exists($f)) {
            return $f;
         }
      }

      $this->error ("FindTemplate: file $file does not exist anywhere in " . implode(' ', $this->ROOT), true);
      return false;
   }


   //
   // Description
   //    Load a template into memory from the underlying file.
   //
   function &load ($file) {
      $debug = $this->DEBUGALL || $this->DEBUG['load'];
      if (! count($this->ROOT)) {
         if ($debug)
            $this->logwrite ("load: cannot open template $file, template base directory not set");
         $this->error ("cannot open template $file, template base directory not set", true);
         return false;
      } else {
         $contents = '';

         $filename = $this->FindTemplate ($file);

         if ($filename)
            $contents = implode ('', (@file($filename)));
         if (!($contents) or (empty($contents)) or (! $filename)) {
            if ($debug)
               $this->logwrite ("load: failed to load $file, $php_errormsg");
            $this->error ("load($file) failure: $php_errormsg", true);
         } else {
            if ($debug)
               $this->logwrite ("load: found $filename");
            return $contents;
         }
      }
   }

   //
   // Description
   //    Recursive internal parse routine.  This will recursively parse a
   //    template containing dynamic inferior templates.  Each of these
   //    inferior templates gets their own entry in the TEMPLATE array.
   //
   
   function &parse_internal_1 ($tag, $rest = '') {
      $debug = $this->DEBUGALL || $this->DEBUG['parse_internal_1'];
      if (empty($tag)) {
         $this->error ("parse_internal_1: empty tag invalid", true);
      }
      if ($debug)
         $this->logwrite ("parse_internal_1 (tag=$tag, rest=$rest)");
      while (!empty($rest)) {
         if ($debug)
            $this->logwrite ("parse_internal_1: REGEX_DYNBEG search: rest => $rest");
          $dynbeg = array();
         if (preg_match ($this->REGEX_DYNBEG, $rest, $dynbeg)) {
            // Found match, now split into two pieces and search the second
            // half for the matching END.  The string which goes into the
            // next element includes the HTML comment which forms the BEGIN
            // block.
            if ($debug)
               $this->logwrite ("parse_internal_1: match beg => $dynbeg[1]");
            $pos = strpos ($rest, $dynbeg[1]);

            // See if the text on either side of the BEGIN comment is only
            // whitespace.  If so, we delete the entire line.
            $okay = false;
            for ($offbeg = $pos - 1; $offbeg >= 0; $offbeg--) {
               $c = $rest{$offbeg};
               if ($c == "\n") {
                  $okay = true;
                  $offbeg++;
                  break;
               }
               if (($c != ' ') && ($c != "\t")) {
                  $offbeg = $pos;
                  break;
               }
            }
            if (! $okay) {
               $offend = $pos + strlen($dynbeg[1]);
            } else {
               $l = strlen ($rest);
               for ($offend = $pos + strlen($dynbeg[1]); $offend < $l; $offend++) {
                  $c = $rest{$offend};
                  if ($c == "\n") {
                     $offend++;
                     break;
                  }
                  if (($c != ' ') && ($c != "\t")) {
                     $offend = $pos + strlen($dynbeg[1]);
                     break;
                  }
               }
            }

            // This includes the contents of the REGEX_DYNBEG in the output
            // $part[] = substr ($rest, 0, $pos);
            // This preserves whitespace on the END block line(s).
            // $part[] = substr ($rest, 0, $pos+strlen($dynbeg[1]));
            // $rest = substr ($rest, $pos+strlen($dynbeg[1]));
            // Catch case where BEGIN block is at position 0.
            if ($offbeg > 0)
               $part[] = substr ($rest, 0, $offbeg);
            $rest = substr ($rest, $offend);
            $sub = '';
            if ($debug)
               $this->logwrite ("parse_internal_1: found at pos = $pos");
            // Okay, here we are actually NOT interested in just the next
            // END block.  We are only interested in the next END block that
            // matches this BEGIN block.  This is not the most efficient
            // because we really could do this in one pass through the
            // string just marking BEGIN and END blocks.  But the recursion
            // makes for a simple algorithm (if there was a reverse
            // preg...).
            $found  = false;
            $dynend = array();
            while (preg_match ($this->REGEX_DYNEND, $rest, $dynend)) {
               if ($debug)
                  $this->logwrite ("parse_internal_1: REGEX_DYNEND search: rest => $rest");
               if ($debug)
                  $this->logwrite ("parse_internal_1: match beg => $dynend[1]");
               $pos  = strpos ($rest, $dynend[1]);
               if ($dynbeg[2] == $dynend[2]) {
                  $found  = true;
                  // See if the text on either side of the END comment is
                  // only whitespace.  If so, we delete the entire line.
                  $okay = false;
                  for ($offbeg = $pos - 1; $offbeg >= 0; $offbeg--) {
                     $c = $rest{$offbeg};
                     if ($c == "\n") {
                        $offbeg++;
                        $okay = true;
                        break;
                     }
                     if (($c != ' ') && ($c != "\t")) {
                        $offbeg = $pos;
                        break;
                     }
                  }
                  if (! $okay) {
                     $offend = $pos + strlen($dynend[1]);
                  } else {
                     $l = strlen ($rest);
                     for ($offend = $pos + strlen($dynend[1]); $offend < $l; $offend++) {
                        $c = $rest{$offend};
                        if ($c == "\n") {
                           $offend++;
                           break;
                        }
                        if (($c != ' ') && ($c != "\t")) {
                           $offend = $pos + strlen($dynend[1]);
                           break;
                        }
                     }
                  }
                  // if ($debug)
                  // $this->logwrite ("parse_internal_1: DYNAMIC BEGIN: (pos,len,beg,end) => ($pos, " . strlen($dynbeg[1]) . ", $offbeg, $offend)
                  // This includes the contents of the REGEX_DYNEND in the output
                  // $rest = substr ($rest, $pos);
                  // This preserves whitespace on the END block line(s).
                  // $rest = substr ($rest, $pos+strlen($dynend[1]));
                  // $sub .= substr ($rest, 0, $pos);
                  $sub .= substr ($rest, 0, $offbeg);
                  $rest = substr ($rest, $offend);
                  // Already loaded templates will not be reloaded.
                  if ($this->TEMPLATE[$dynend[2]]['clear']) {
                     $this->TEMPLATE[$dynend[2]]['string']  = '';
                     $this->TEMPLATE[$dynend[2]]['result'] = '';
                     $this->TEMPLATE[$dynend[2]]['part']    =
                        $this->parse_internal_1 ($dynend[2], ' ');
                  } else if (!$this->TEMPLATE[$dynend[2]]['loaded']) {
                     // Omit pathological case of empty dynamic template.
                     if (strlen($sub) > 0) {
                        $this->TEMPLATE[$dynend[2]]['string'] = $sub;
                        $this->TEMPLATE[$dynend[2]]['part']   =
                           $this->parse_internal_1 ($dynend[2], $sub);
                        $this->TEMPLATE[$dynend[2]]['part']['parent'] = $tag;
                     }
                  }
                  $this->TEMPLATE[$dynend[2]]['loaded'] = true;
                  $part[] = &$this->TEMPLATE[$dynend[2]];
                  $this->TEMPLATE[$dynend[2]]['tag']    = $dynend[2];
                  break;
               } else {
                  $sub .= substr ($rest, 0, $pos+strlen($dynend[1]));
                  $rest = substr ($rest, $pos+strlen($dynend[1]));
                  if ($debug)
                     $this->logwrite ("parse_internal_1: $dynbeg[2] != $dynend[2]");
               }
            }
            if (!$found) {
               $this->error ("malformed dynamic template, missing END<BR />\n" .
                             "$dynbeg[1]<BR />\n", true);
            }
         } else {
            // Although it would appear to make sense to check that we don't
            // have a dangling END block, we will, in fact, ALWAYS appear to
            // have a dangling END block.  We stuff the BEGIN string in the
            // part before the inferior template and the END string in the
            // part after the inferior template.  So for this test to work,
            // we would need to look just past the final match.
            $dynend = array();
            if (preg_match ($this->REGEX_DYNEND, $rest, $dynend)) {
               // $this->error ("malformed dynamic template, dangling END<BR />\n" .
               //            "$dynend[1]<BR />\n", 1);
            }
            $part[] = $rest;
            $rest = '';
         }
      }
      return $part;
   }

   //
   // Description
   //    Parse the template.  If $tag is actually an array, we iterate over
   //    the array elements.  If it is a simple string tag, we may still
   //    recursively parse the template if it contains dynamic templates and
   //    we are configured to automatically load those as well.
   //
   function parse_internal ($tag) {
      $debug = $this->DEBUGALL || $this->DEBUG['parse_internal'];
      $append = false;
      if ($debug)
         $this->logwrite ("parse_internal (tag=$tag)");

      // If we are handed an array of tags, iterate over all of them.  This
      // is really a holdover from the way class.FastTemplate.php3 worked;
      // I think subst() already pulls that array apart for us, so this
      // should not be necessary unless someone calls the internal member
      // function directly.
      if (gettype($tag) == 'array') {
         reset ($tag);
         foreach ($tag as $t) {
            $this->parse_internal ($t);
         }
      } else {
         // Load the file if it hasn't already been loaded.  It might be
         // nice to put in some logic that reloads the file if it has
         // changed since we last loaded it, but that probably gets way too
         // complicated and only makes sense if we start keeping it floating
         // around between page loads as a persistent variable.
         if (!isset($this->TEMPLATE[$tag]['loaded'])) {
	    if ($this->TEMPLATE[$tag]['dynamic']) {
	       // Template was declared via define_dynamic().
	       if ($this->TEMPLATE[$tag]['parent'])
		  $tag = $this->TEMPLATE[$tag]['parent'];
	       else {
		  // Try to find a non-dynamic template with the same file.
		  // This would have been defined via define(array(), true)
		  reset ($this->TEMPLATE);
		  foreach (array_keys($this->TEMPLATE) as $ptag) {
		     if ($debug)
			$this->logwrite ("parse_internal: looking for non-dynamic parent, $ptag");
		     if (!$this->TEMPLATE[$ptag]['dynamic']
			 && ($this->TEMPLATE[$ptag]['file'] == $this->TEMPLATE[$tag]['file'])) {
			$tag = $ptag;
			break;
		     }
		  }
	       }
	    }
	    $this->TEMPLATE[$tag]['string'] = &$this->load($this->TEMPLATE[$tag]['file']);
	    $this->TEMPLATE[$tag]['loaded'] = 1;
         }

         // If we are supposed to automatically detect dynamic templates and the dynamic
         // flag is not set, scan the template for dynamic sections.  Dynamic sections
         // markers have a very rigid syntax as HTML comments....
         if ($this->DYNAMIC) {
            $this->TEMPLATE[$tag]['tag']  = $tag;
            if (! $this->TEMPLATE[$tag]['parsed']) {
               $this->TEMPLATE[$tag]['part'] = $this->parse_internal_1 ($tag, $this->TEMPLATE[$tag]['string']);
               $this->TEMPLATE[$tag]['parsed'] = true;
            }
         }
      }
   }

   //
   // Description
   //    class.FastTemplate.php3 compatible interface.
   //
   // Notes
   //    I prefer the name `subst' to `parse' since during this phase we are
   //    really doing variable substitution into the template.  However, at
   //    some point we have to load and parse the template and `subst' will
   //    do that as well...
   //
   function parse ($handle, $tag, $autoload = true) {
      return $this->subst ($handle, $tag, $autoload);
   }

   //
   // Description
   //    Perform substitution on the template.  We do not really recurse
   //    downward in the sense that we do not do subsitutions on inferior
   //    templates.  For each inferior template which is a part of this
   //    template, we insert the current value of their results.
   //
   // Notes
   //    Do I want to make this return a reference?
   function subst ($handle, $tag, $autoload = true) {
      $append = false;
      $debug = $this->DEBUGALL || $this->DEBUG['subst'];
      $this->LAST = $handle;

      if ($debug)
         $this->logwrite ("subst (handle=$handle, tag=$tag, autoload=$autoload)");

      if (gettype($tag) == 'array') {
         $count = 0;
         foreach (array_values($tag) as $t) {
            if ($count != 0)
               $t = ".$t";
            $count ++;
            if ($debug)
               $this->logwrite ("subst: calling subst($handle,$t,$autoload)");
            $this->subst ($handle, $t, $autoload);
         }
         return $this->HANDLE[$handle];
      }

      // Period prefix means append result to pre-existing value.
      if (substr($tag,0,1) == '.') {
         $append = true;
         $tag = substr ($tag, 1);
         if ($debug)
            $this->logwrite ("subst (handle=$handle, tag=$tag, autoload=$autoload) in append mode");
      }
      // $this->TEMPLATE[$tag] will only be set if it was explicitly
      // declared via define(); i.e., inferior templates will not have an
      // entry.
      if (isset($this->TEMPLATE[$tag])) {
         if (! $this->TEMPLATE[$tag]['parsed'])
            $this->parse_internal ($tag);
      } else {
         if (!$this->DYNAMIC) {
            $this->error ("subst (handle=$handle, tag=$tag, autoload=$autoload): " .
                          'no such tag and dynamic templates are turned off', true);
         }
         if ($autoload) {
            if ($debug)
               $this->logwrite ("subst: TEMPLATE[tag=$tag] not found, trying autoload");
            foreach (array_keys($this->TEMPLATE) as $t) {
               if ($debug)
                  $this->logwrite ("subst: calling parse_internal (tag=$t)");
               if (! $this->TEMPLATE[$tag]['parsed'])
                  $this->parse_internal ($t);
            }
            if ($debug)
               $this->logwrite ('subst: retrying with autoload = false');
            $this->subst ($handle, $tag, false);
            if ($debug)
               $this->logwrite ('subst: completed with autoload = false');
            return;
         } else {
            $this->error ("subst (handle=$handle, tag=$tag, autoload=$autoload):  no such tag", true);
         }
      }
      if (!$append) {
         $this->TEMPLATE[$tag]['result'] = '';
         if ($debug)
            $this->logwrite ("subst (handle=$handle, tag=$tag, autoload=$autoload) in overwrite mode");
      }
      if ($debug)
         $this->logwrite ("subst: type(this->TEMPLATE[$tag]['part']) => " .
                          gettype($this->TEMPLATE[$tag]['part']));
      // Hmmm, clear() called before subst() seems to result in this not
      // being defined which leaves me a bit confused....
      $result = '';
      if (isset($this->TEMPLATE[$tag]['part'])) {
         reset ($this->TEMPLATE[$tag]['part']);
         foreach (array_keys($this->TEMPLATE[$tag]['part']) as $p) {
	    if ($debug)
	       $this->logwrite ("subst: looking at TEMPLATE[$tag]['part'][$p]");
	    $tmp = $this->TEMPLATE[$tag]['part'][$p];
	    // Don't try if ($p == 'parent')....
	    if (strcmp ($p, 'parent') == 0) {
	       if ($debug)
		  $this->logwrite ("subst: skipping part $p");
	       $tmp = '';
	    } else if (gettype($this->TEMPLATE[$tag]['part'][$p]) == 'string') {
	       if ($debug)
		  $this->logwrite ("subst: using part $p");
	       reset ($this->VAR);
	       // Because we treat VAR and HANDLE separately (unlike
	       // class.FastTemplate.php3), we have to iterate over both or we
	       // miss some substitutions and are not 100% compatible.
	       while (list($key,$val) = each ($this->VAR)) {
		  if ($debug)
		     $this->logwrite ("subst: substituting VAR $key = $val in $tag");
		  $key = '{'.$key.'}';
		  $tmp = str_replace ($key, $val, $tmp);
	       }
	       reset ($this->HANDLE);
	       while (list($key,$val) = each ($this->HANDLE)) {
		  if ($debug)
		     $this->logwrite ("subst: substituting HANDLE $key = $val in $tag");
		  $key = '{'.$key.'}';
		  $tmp = str_replace ($key, $val, $tmp);
	       }
	       $result .= $tmp;
	    } else {
	       $xtag = $this->TEMPLATE[$tag]['part'][$p]['tag'];
	       if ($this->debug) {
		  $this->logwrite ("subst: substituting other tag $xtag result in $tag");
	       }
	       $result .= $this->TEMPLATE[$xtag]['result'];
	    }
         }
      }
      if ($this->STRICT) {
      	$matches = array();
         if (preg_match ($this->REGEX_VAR, $result, $matches) && $this->VerifyUnmatched == "yes") {
            $tstr = "";
            while (list($tkey,$tval) = @each($matches)){
               $tstr .= $tval;
            }
            $this->error ("<B>unmatched tags still present in $tag</B> ($handle - $tstr)<BR/>");
         }
      } else {
         $result = preg_replace ($this->REGEX_VAR, '', $result);
      }
      if ($append) {
         if ($debug) {
            $this->logwrite ("subst: appending TEMPLATE[$tag]['result'] = $result");
	    $this->logwrite ("subst: old HANDLE[$handle] = {$this->HANDLE[$handle]}");
	    $this->logwrite ("subst: old TEMPLATE[$tag]['result'] = {$this->TEMPLATE[$tag]['result']}");
	 }
         $this->HANDLE[$handle] .= $result;
         $this->TEMPLATE[$tag]['result'] .= $result;
	 if ($debug) {
	    $this->logwrite ("subst: new HANDLE[$handle] = {$this->HANDLE[$handle]}");
	    $this->logwrite ("subst: new TEMPLATE[$tag]['result'] = {$this->TEMPLATE[$tag]['result']}");
	 }

      } else {
         if ($debug)
            $this->logwrite ("subst: setting TEMPLATE[$tag]['result'] = $result");
         $this->HANDLE[$handle]  = $result;
         $this->TEMPLATE[$tag]['result'] = $result;
      }
      return $this->HANDLE[$handle];
   }

   //
   // Description
   //    Clear a block from a template.  The intent is to remove an inferior
   //    template from a parent.  This works even if the template has
   //    already been parsed since we go straight to the specified template
   //    and clear the results element.
   //
   function clear_dynamic ($tag) {
      $this->clear($tag);
   }

   function clear ($tags) {
		if(is_string($tags)) $tags = array($tags);
		foreach($tags as $tag)
		{
			if(!isset($this->TEMPLATE[$tag])) continue;
			//$this->TEMPLATE[$tag]['loaded']  = true;
      		//$this->TEMPLATE[$tag]['string']  = '';
      		$this->TEMPLATE[$tag]['result'] = '';
      		//$this->TEMPLATE[$tag]['clear']   = true;
		}
   }

   //
   // Description
   //   Clears all information associated with the specified tag as well as
   //   any information associated with embedded templates.  This will force
   //   the templates to be reloaded on the next call to subst().
   //   Additionally, any results of previous calls to subst() will also be
   //   cleared.
   //
   // Notes
   //   This leaves dangling references in $this->HANDLE.  Or does PHP do
   //   reference counting so they are still valid?
   //
   function unload ($tag) {
      if (!isset($this->TEMPLATE[$tag]))
         return;
      if (isset ($this->TEMPLATE[$tag]['parent'])) {
         $ptag = $this->TEMPLATE[$tag]['parent'];
         foreach (array_keys($this->TEMPLATE) as $t) {
            if ($this->TEMPLATE[$t]['parent'] == $ptag) {
               unset ($this->TEMPLATE[$t]);
            }
         }
      }
      unset ($this->TEMPLATE[$tag]);
      return;
   }

   //
   // Description
   //    class.FastTemplate.php3 compatible interface.
   //
   function assign ($tplkey, $rest = '') {
      $this->setkey ($tplkey, $rest);
   }

   //
   // Description
   //    Set a (key,value) in our internal variable array.  These will be
   //    used during the substitution phase to replace template variables.
   //
   function setkey ($tplkey, $rest = '') {
      if (gettype ($tplkey) == 'array') {
         reset ($tplkey);
         while (list($key,$val) = each ($tplkey)) {
            if (!empty($key)) {
               $this->VAR[$key] = $val;
            }
         }
      } else {
         if (!empty($tplkey)) {
            $this->VAR[$tplkey] = $rest;
         }
      }
   }

   //
   // Description
   //    class.FastTemplate.php3 compatible interface
   //
   function get_assigned ($key = '') {
      return $this->getkey ($key);
   }

   //
   // Description
   //    Retrieve a value from our internal variable array given the key name.
   //
   function getkey ($key = '') {
      if (empty($key)) {
         return false;
      } else if (isset ($this->VAR[$key])) {
         return $this->VAR[$key];
      } else {
         return false;
      }
   }

   function FastGet($handle = ''){
      return $this->fetch($handle);
   }

   function fetch ($handle = '') {
      if (empty($handle)) {
	 $handle = $this->LAST;
      }
      return $this->HANDLE[$handle];
   }

   function xprint ($handle = '') {
      if (empty($handle)) {
	 $handle = $this->LAST;
      }
      print ($this->HANDLE[$handle]);
   }

   function FastPrint ($handle = '') {
      $this->xprint ($handle);
   }

   function clear_href ($key = '') {
      $this->unsetkey ($key);
   }

   function unsetkey ($key = '') {
      if (empty($key)) {
         unset ($this->VAR);
         $this->VAR = array ();
      } else if (gettype($key) == 'array') {
         reset ($key);
         foreach (array_values($key) as $k) {
            unset ($this->VAR[$k]);
         }
      } else {
         unset ($this->VAR[$key]);
      }
   }

   //
   // Description
   //    Member function to control explicit error messages.  We don't do
   //    real PHP error handling.
   //
   function error ($errorMsg, $die = 0) {
      $this->ERROR = $errorMsg;
      echo "ERROR: {$this->ERROR} <br /> \n";
      if ($die) {
         exit;
      }
      return;
   }

   function regex_type($tp =""){
      if ('' == $tp){
         return $this->REGEX_VAR;
      }
      else{
         if ($this->REGEX_VAR_ARRAY[$tp] != ""){
            $this->REGEX_VAR = $this->REGEX_VAR_ARRAY[$tp];
         }
         else{
            $this->error("Unknown REGEX_VAR type");
         }
      }
   }


}
}
?>