// From script.aculo.us 1.6.5, compressed by jsmin (http://www.crockford.com/javascript/jsmin.html).

var Scriptaculous={Version:'1.6.5',require:function(libraryName){document.write('<script type="text/javascript" src="'+libraryName+'"></script>');},load:function(){if((typeof Prototype=='undefined')||(typeof Element=='undefined')||(typeof Element.Methods=='undefined')||parseFloat(Prototype.Version.split(".")[0]+"."+
Prototype.Version.split(".")[1])<1.5)
throw("script.aculo.us requires the Prototype JavaScript framework >= 1.5.0");$A(document.getElementsByTagName("script")).findAll(function(s){return(s.src&&s.src.match(/scriptaculous\.js(\?.*)?$/))}).each(function(s){var path=s.src.replace(/scriptaculous\.js(\?.*)?$/,'');var includes=s.src.match(/\?.*load=([a-z,]*)/);(includes?includes[1]:'builder,effects,dragdrop,controls,slider').split(',').each(function(include){Scriptaculous.require(path+include+'.js')});});}}
Scriptaculous.load();