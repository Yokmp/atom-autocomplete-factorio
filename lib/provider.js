'use babel';

// data source is an array of objects
import suggestions from './api';

class FactorioProvider {
	constructor() {
		// offer suggestions only when editing plain text or HTML files
		this.selector = '.source.lua';
	}

	getSuggestions(options) {
		const { prefix } = options;

		// only look for suggestions after 2 characters have been typed
		if (prefix.length >= 2) {
			return this.findMatchingSuggestions(prefix);
		}
	}

	findMatchingSuggestions(prefix) {
		// filter list of suggestions to those matching the prefix, case insensitive
		let prefixLower = prefix.toLowerCase();
		let matchingSuggestions = suggestions.filter((suggestion) => {
			let textLower = suggestion.text.toLowerCase();
			return textLower.startsWith(prefixLower);
		});

		// run each matching suggestion through inflateSuggestion() and return
		return matchingSuggestions.map(this.inflateSuggestion);
	}

	// clones a suggestion object to a new object with some shared additions
	// cloning also fixes an issue where selecting a suggestion won't insert it
	inflateSuggestion(suggestion) {
		return {
			text: suggestion.text,
			description: suggestion.description,
			descriptionMoreURL: "http://lua-api.factorio.com/latest/"+suggestion.label+".html#"+suggestion.label+"."+suggestion.text,
			type: suggestion.type,
			leftLabel: suggestion.label,
			rightLabel: suggestion.version
		};
	}
}
export default new FactorioProvider();
