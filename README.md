# atom-autocomplete-factorio
Factorio Lua API autocomplete for Atom.
Factorio version is 0.15.33. To create one for your version please scoll down.

[Forum topic - Autocomplete for Atom](https://forums.factorio.com/viewtopic.php?f=135&t=31456&sid=f324b0d762343de5332f9a132fc5aa08)

### How to create Suggestions for another Version?

First things first:
You need a couple of extra Programms.

+ PHP 5.x
+ Cygwin or Mingw(untested)

+ Download and install both Programs. Install wget for Cygwin/Mingw if you want the latest Version from lua-api.factorio.com.
+ Get this Repository.
+ Now open this Repo and navigate to autogen/ in your Filemanager.
+ startup Cygwin and navigate to autogen/ too.
+ open start.sh in a Texteditor.
+ Change the configuration Path to your doc-html folder of your Factorio installation

```pathtodocs="/cygdrive/d/Factorio/doc-html" # /cygdrive/d/ is your drive D:\ on Windows```

- execute ```source start.sh```
- Wait.
- Restart Atom ([CTRL]+[SHIFT]+[F5]).
