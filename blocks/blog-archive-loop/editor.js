import { registerBlockType } from '@wordpress/blocks';
import ServerSideRender from '@wordpress/server-side-render';

import metadata from './block.json';

registerBlockType( metadata.name, {
	...metadata,
	edit() {
		return (
			<ServerSideRender block="mccullough-digital/blog-archive-loop" />
		);
	},
	save() {
		return null;
	},
} );
