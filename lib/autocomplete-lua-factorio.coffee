AutocompleteLuaFactorioView = require './autocomplete-lua-factorio-view'
{CompositeDisposable} = require 'atom'

module.exports = AutocompleteLuaFactorio =
  autocompleteLuaFactorioView: null
  modalPanel: null
  subscriptions: null

  activate: (state) ->
    @autocompleteLuaFactorioView = new AutocompleteLuaFactorioView(state.autocompleteLuaFactorioViewState)
    @modalPanel = atom.workspace.addModalPanel(item: @autocompleteLuaFactorioView.getElement(), visible: false)

    # Events subscribed to in atom's system can be easily cleaned up with a CompositeDisposable
    @subscriptions = new CompositeDisposable

    # Register command that toggles this view
    @subscriptions.add atom.commands.add 'atom-workspace', 'autocomplete-lua-factorio:toggle': => @toggle()

  deactivate: ->
    @modalPanel.destroy()
    @subscriptions.dispose()
    @autocompleteLuaFactorioView.destroy()

  serialize: ->
    autocompleteLuaFactorioViewState: @autocompleteLuaFactorioView.serialize()

  toggle: ->
    console.log 'AutocompleteLuaFactorio was toggled!'

    if @modalPanel.isVisible()
      @modalPanel.hide()
    else
      @modalPanel.show()
