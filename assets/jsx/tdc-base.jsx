(function(){
	"use strict";

	const { Component } = wp.element;

	class TdC_Component extends Component {
		constructor( props ) {
			super( props );

			var properties = Object.getOwnPropertyNames( this.__proto__ ),
				ignoredMethods = [ 'constructor', 'componentDidMount', 'render', 'componentWillMount', 'componentWillReceiveProps', 'shouldComponentUpdate', 'componentWillUpdate', 'componentDidUpdate', 'componentWillUnmount' ];
	
			for (var i = 0; i < properties.length; i++) {
				if ( -1 !== ignoredMethods.indexOf( properties[i] ) || 'function' !== typeof this[ properties[i] ] ) {
					continue;
				}
	
				this[ properties[i] ] = this[ properties[i] ].bind( this );
			}
		}

		_onTextAttributeChange( key, text ) {
			var attributes = {};
			attributes[key] = text;
			this.props.setAttributes( attributes );
		}
	}

	window.TdC_Component = TdC_Component;
})();