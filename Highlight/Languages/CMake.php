<?php
/* Copyright (c)
 * - 2006-2013, Igor Kalnitsky (igor.kalnitsky@gmail.com), highlight.js
 *              (original author)
 * - 2013,      Geert Bergman (geert@scrivo.nl), highlight.php
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 * 3. Neither the name of "highlight.js", "highlight.php", nor the names of its
 *    contributors may be used to endorse or promote products derived from this
 *    software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

/**
 * This file is a direct port of cmake.js, the CMake language 
 * definition file for highlight.js, to PHP.
 * @see https://github.com/isagalaev/highlight.js
 */
namespace Highlight\Languages;

use Highlight\Language;
use Highlight\Mode;

class CMake extends Language {
	
	protected function getName() {
		return "cmake";
	}
	
	protected function isCaseInsensitive() {
		return true;
	}
	
	protected function getKeyWords() {
		return 
			"add_custom_command add_custom_target add_definitions add_dependencies " .
			"add_executable add_library add_subdirectory add_test aux_source_directory " .
			"break build_command cmake_minimum_required cmake_policy configure_file " .
			"create_test_sourcelist define_property else elseif enable_language enable_testing " .
			"endforeach endfunction endif endmacro endwhile execute_process export find_file " .
			"find_library find_package find_path find_program fltk_wrap_ui foreach function " .
			"get_cmake_property get_directory_property get_filename_component get_property " .
			"get_source_file_property get_target_property get_test_property if include " .
			"include_directories include_external_msproject include_regular_expression install " .
			"link_directories load_cache load_command macro mark_as_advanced message option " .
			"output_required_files project qt_wrap_cpp qt_wrap_ui remove_definitions return " .
			"separate_arguments set set_directory_properties set_property " .
			"set_source_files_properties set_target_properties set_tests_properties site_name " .
			"source_group string target_link_libraries try_compile try_run unset variable_watch " .
			"while build_name exec_program export_library_dependencies install_files " .
			"install_programs install_targets link_libraries make_directory remove subdir_depends " .
			"subdirs use_mangled_mesa utility_source variable_requires write_file";
	}

	protected function getContainedModes() {
				
		return array(
			new Mode(array(
				"className" => "envvar",
				"begin" => "\\\${", 
				"end" => "}"
			)),
			$this->HASH_COMMENT_MODE,
			$this->QUOTE_STRING_MODE,
			$this->NUMBER_MODE		
		);
		
	}

}

?>