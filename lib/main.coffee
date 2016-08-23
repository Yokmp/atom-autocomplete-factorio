provide: ->
    @aprovider_factrio_lua_api

provider =
  selector: '.source.lua'
  #disableForSelector: '.source.lua .comment'

  inclusionPriority: 1
  excludeLowerPriority: true

  getSuggestions: ({editor, bufferPosition, scopeDescriptor, prefix, activatedManually}) ->
    new Promise (resolve) ->
      resolve([snippet: 'any'])

  onDidInsertSuggestion: ({editor, triggerPosition, suggestion}) ->
    # nothing to do atm

  # cleanup & unsub & kill
  dispose: ->
