/* ------------------------------------------------------------------------------------------------ */
/* Retrieves the translation of text. 																*/
/* @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/ 		*/
/* ------------------------------------------------------------------------------------------------ */
import { __ } from '@wordpress/i18n';

/* -------------------------------------------------------------------------------------------------------------------- */
/* React hook that is used to mark the block wrapper element. 															*/
/* It provides all the necessary props like the class name. 															*/
/* @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops     */
/* -------------------------------------------------------------------------------------------------------------------- */
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';

/* --------------------------------------------------------------------------------------------- */
/* Date module for WordPress.																	 */
/* @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-date/	 */
/* --------------------------------------------------------------------------------------------- */
import { format, dateI18n, getSettings } from '@wordpress/date';

/* --------------------------------------------------------------------------------------------- */
/* WordPress’ data module serves as a hub to manage application state for both plugins 			 */
/* and WordPress itself, providing tools to manage data within and between distinct modules.	 */
/* @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-data/	 */
/* --------------------------------------------------------------------------------------------- */
import { useSelect } from '@wordpress/data';

/* --------------------------------------------------------------------------------------------- */
/* This package includes a library of generic WordPress components to be used for creating 		 */
/* common UI elements shared between screens and features of the WordPress dashboard.			 */
/* @see https://developer.wordpress.org/block-editor/reference-guides/components/	 			 */
/* --------------------------------------------------------------------------------------------- */
import { PanelBody, ToggleControl, FormTokenField } from '@wordpress/components';

/* --------------------------------------------------------------------------------------------- */
/* Element is, quite simply, an abstraction layer atop React.									 */
/* @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-element/ */
/* --------------------------------------------------------------------------------------------- */
import { RawHTML } from '@wordpress/element';

/* --------------------------------------------------------------------------------------------- */
/* Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files. 				 */
/* Those files can contain any CSS code that gets applied to the editor. 						 */
/* @see https://www.npmjs.com/package/@wordpress/scripts#using-css 								 */
/* --------------------------------------------------------------------------------------------- */
import './editor.scss';

/* ------------------------------------------------------------------------------------------------------ */
/* The edit function describes the structure of your block in the context of the						  */
/* editor. This represents what the editor will render when the block is used. 						  	  */
/* @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit 	  */
/* @return {WPElement} Element to render. 																  */
/* ------------------------------------------------------------------------------------------------------ */
export default function Edit({ attributes, setAttributes }) {

	// get the 'attributes' we defined in block.json
	const { displayFeaturedImage, selectedPostIds } = attributes;

	/* -------------------------------------------------------------------------------------------------------- */
	/* useSelect() react hook select Data from WordPress Data store (wp.data api)
	/* @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-data/#useselect 	*/
	/* When you run getEntityRecords(), WordPress sends a request using rest API to get the data				*/
	/* Rest API handbook to fetch post data @see https://developer.wordpress.org/rest-api/reference/posts/		*/
	/* -------------------------------------------------------------------------------------------------------- */
	const displayPosts = useSelect(
		(select) => {
			return select('core').getEntityRecords('postType', 'post', {
				_embed: true,
				include: selectedPostIds,
				orderby: 'include',
			});
		},
		[selectedPostIds]
	);

	// This callback recieves the value from the ToggleControl and set the new value to 'displayFeaturedImage' attribute
	const onDisplayFeaturedImageChange = (value) => {
		setAttributes({ displayFeaturedImage: value });
	};

	// Get all posts to later create postSuggestions and postSelected array
	const allPosts = useSelect((select) => {
		return select('core').getEntityRecords('postType', 'post', {
			per_page: -1,
		});
	}, []);

	// Initializes postSuggestions - an array of strings - will contain all sitewide posts' titles
	let postSuggestions = [];

	// Initializes postSelected - an array of strings - will contain some selected posts' titles
	let postSelected = [];

	if (allPosts) {

		/* ------------------------------------------------------------------------------------------------ */
		/* Now create postSuggestions array - to present to the user as suggested tokens - used  			*/
		/* in FormTokenField's 'suggestions' property. Details of the FormTokenField's, 					*/
		/* @see https://developer.wordpress.org/block-editor/reference-guides/components/form-token-field/ 	*/
		/* ------------------------------------------------------------------------------------------------ */
		postSuggestions = allPosts.map(post => post.title.rendered);

		/* ------------------------------------------------------------------------------------------------------------ */
		/* Now create postSelected array - an array of posts' titles that match the posts IDs of the  					*/
		/* currently selected posts titles in FormTokenField's 'value' property - to display as tokens in the field 	*/
		/* ------------------------------------------------------------------------------------------------------------ */
		postSelected = selectedPostIds.map((postId) => {

			// purpose - to convert posts IDs back to the posts titles and return
			const currentPost = allPosts.find((post) => {
				return post.id === postId;
			});

			// return false, if no match
			if (currentPost === undefined || !currentPost) {
				return false;
			}

			// return selected post title
			return currentPost.title.rendered;
		});

	}

	//on FormTokenField's onChange, convert the selected posts' titles (tokens) into the posts IDs before saving it
	const onSelectedPostChange = (tokens) => {

		// Initializes and create newSelectedPostIds - an array of number - array of selected post ids converted from the tokens
		let newSelectedPostIds = [];

		newSelectedPostIds = tokens.map((postTitle) => {

			const matchingPost = allPosts.find((post) => {
				return post.title.rendered === postTitle;

			});

			// return false, if no match
			if (matchingPost === undefined || !matchingPost) {
				return false;
			}

			return matchingPost.id;
		});

		// update selectedPostIds array
		setAttributes({ selectedPostIds: newSelectedPostIds });
	};

	return (
		<>
			<InspectorControls>
				<PanelBody>
					<ToggleControl
						label={__('Display featured image', 'ej-recommended-posts')}
						checked={displayFeaturedImage}
						onChange={onDisplayFeaturedImageChange}
					/>
					<FormTokenField
						label={__('Type and select posts', 'ej-recommended-posts')}
						value={postSelected}
						suggestions={postSuggestions}
						onChange={onSelectedPostChange}
					/>
				</PanelBody>
			</InspectorControls>
			<div {...useBlockProps()}>
				{
					// if we have posts, then loop and return each post
					displayPosts &&
					displayPosts.map((post) => {
						// some check to ensure post has the featured image
						const featuredImage =
							post._embedded &&
							post._embedded['wp:featuredmedia'] &&
							post._embedded['wp:featuredmedia'].length > 0 &&
							post._embedded['wp:featuredmedia'][0];

						// some check to ensure post has the category assigned
						const postCategory =
							post._embedded &&
							post._embedded['wp:term'] &&
							post._embedded['wp:term'].length > 0 &&
							post._embedded['wp:term'][0][0];

						// return all the post data
						return (
							<div key={post.id} className='wp-block-ej-blocks-ej-recommended-posts__item'>
								{displayFeaturedImage && featuredImage && (
									<a href={post.link} className='wp-block-ej-blocks-ej-recommended-posts__item-image'>
										<img
											src={
												// if the image is small, by default in the editor side, 'large' size is not returend. in this case load 'full' size
												featuredImage.media_details.sizes.large ? (
													featuredImage.media_details.sizes.large.source_url
												) : (
													featuredImage.media_details.sizes.full.source_url
												)
											}
											alt={featuredImage.alt_text}
										/>
									</a>
								)}
								<div className='wp-block-ej-blocks-ej-recommended-posts__item-title'>
									<a href={post.link}>
										{post.title.rendered ? (
											<RawHTML>
												{post.title.rendered}
											</RawHTML>
										) : (
											__('(No title)', 'ej-recommended-posts')
										)}
									</a>
								</div>
								{post.date_gmt && (
									<time dateTime={format('c', post.date_gmt)}>
										{dateI18n(
											getSettings().formats.date,
											post.date_gmt
										)}
									</time>
								)}
								<div className='wp-block-ej-blocks-ej-recommended-posts__item-meta'>
									{postCategory && (
										<div className='wp-block-ej-blocks-ej-recommended-posts__item-meta-cat'>
											<a href={postCategory.link}>
												{postCategory.name}
											</a>
										</div>
									)}
									<hr />
									<a href={post.link} className='wp-block-ej-blocks-ej-recommended-posts__item-readmore'>
										<span className='ej-readmore'>
											{__('Read more', 'ej-recommended-posts')}
										</span>
										<span className='ej-readmore-icon' aria-hidden='true'></span>
									</a>
								</div>
							</div>
						);
					})
				}
			</div>
		</>
	);
}
