require(["dojo/node!fs", "dojox/json/ref", "dojo/_base/kernel"], function(fs, ref, kernel){

	var nodeRequire = kernel.global.require && kernel.global.require.nodeRequire;
	var HIGHLIGHT_DIR = dojo.config.highlightJsDir;
	var LANGS_W_DEPS = ['arduino.js']
	
	hljs = nodeRequire(HIGHLIGHT_DIR + "highlight.js");

	var refs = [];

	function regExpsRep(l,p) {
		refs.push(l);
		for (x in {"begin":1, "end":2, "lexemes":3, "illegal":4}) {
			if (l[x] && l[x].source) {
				l[x] = l[x].source;
			}
		}
		for (var i in l) {
			var doneIt = false;
			for (var j=0; j<refs.length; j++) {
				if (refs[j] == l[i]) {
					doneIt = true;
				}
			}
			if (l[i] && typeof l[i] == 'object' && !doneIt) {
				regExpsRep(l[i], l[i]);
			}
		}
	}

	function patch(s) {
		return s.replace(/\\u([0-9A-Fa-f]+)/g, "\\x{$1}");
	}

	function exportLang(lang) {
		var x = nodeRequire(HIGHLIGHT_DIR + "languages/" + lang + ".js");
		var l = x(hljs);
		refs = [];
		regExpsRep(l);
		hljs.registerLanguage(lang, x);
		console.log(lang);
		console.log(patch(dojox.json.ref.toJson(l)));
	}

	fs.readdir(HIGHLIGHT_DIR + "languages/",function(err,files){
		if(err) {
			throw err;
		}

		// Load all of the languages that don't extend other languages
		files.forEach(function(file) {
			if (file == ".DS_Store" || LANGS_W_DEPS.indexOf(file) >= 0) {
				return;
			}
			exportLang(file.replace(/\.js$/, ""));
		});

		// These languages extend other languages, so we need to make sure that
		// they are loaded *after* all the standard languages are loaded.
		LANGS_W_DEPS.forEach(function(file) {
			exportLang(file.replace(/\.js$/, ""));
		});
	});
});
