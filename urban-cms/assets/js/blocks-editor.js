/**
 * Urban CMS — Gutenberg Block Editor Script
 *
 * Registers all Urban CMS blocks using WordPress global APIs.
 * No JSX / no build step required — uses wp.element.createElement directly.
 *
 * Blocks:
 *   urban-cms/district-stats
 *   urban-cms/events-list
 *   urban-cms/business-directory
 *   urban-cms/initiative-progress
 *   urban-cms/team-grid
 *   urban-cms/newsletter
 *   urban-cms/hero-banner
 */

/* global wp, urbanCMSBlocks */

(function () {
  'use strict';

  const { registerBlockType }                 = wp.blocks;
  const { createElement: el, Fragment }       = wp.element;
  const { InspectorControls, useBlockProps }  = wp.blockEditor;
  const {
    PanelBody, PanelRow,
    TextControl, TextareaControl, ToggleControl,
    RangeControl, SelectControl,
    ColorPicker, Placeholder,
    ServerSideRender,
  } = wp.components;
  const { __ } = wp.i18n;

  const data = window.urbanCMSBlocks || {
    eventCategories: [], businessCategories: [],
    initiativeTypes: [], initiatives: [],
  };

  /* =====================================================
     Shared helpers
     ===================================================== */

  /** Wrap options array with an "All" first entry */
  function withAll( options, label ) {
    return [ { value: '', label: label || __( 'All', 'urban-cms' ) }, ...options ];
  }

  /** Count options 1–20 */
  const countOptions = Array.from( { length: 20 }, ( _, i ) => ({
    value: i + 1,
    label: String( i + 1 ),
  }) );

  /**
   * Inspector panel that wraps PanelBody — reduces boilerplate.
   * Usage: inspectorPanel( 'Settings', controlsArray )
   */
  function inspectorPanel( title, controls ) {
    return el( InspectorControls, {},
      el( PanelBody, { title, initialOpen: true }, ...controls )
    );
  }

  /**
   * Server-side preview — shows the live PHP render inside the editor.
   * Falls back to a placeholder while loading.
   */
  function serverPreview( block, attributes ) {
    return el( ServerSideRender, {
      block,
      attributes,
      LoadingResponsePlaceholder: () =>
        el( Placeholder, { label: __( 'Loading preview…', 'urban-cms' ) } ),
      ErrorResponsePlaceholder: () =>
        el( Placeholder, { label: __( 'Preview unavailable in editor.', 'urban-cms' ) } ),
    } );
  }

  /* =====================================================
     1. District Stats Bar
     ===================================================== */
  registerBlockType( 'urban-cms/district-stats', {
    edit( { attributes, setAttributes } ) {
      const bp = useBlockProps();
      const { useCustom } = attributes;

      const fields = [1, 2, 3, 4].flatMap( (i) => [
        el( TextControl, {
          key: `num${i}`,
          label: __( `Stat ${i} — Number`, 'urban-cms' ),
          value: attributes[ `stat${i}Number` ] || '',
          onChange: (v) => setAttributes( { [`stat${i}Number`]: v } ),
        } ),
        el( TextControl, {
          key: `lbl${i}`,
          label: __( `Stat ${i} — Label`, 'urban-cms' ),
          value: attributes[ `stat${i}Label` ] || '',
          onChange: (v) => setAttributes( { [`stat${i}Label`]: v } ),
        } ),
      ] );

      return el( Fragment, {},
        inspectorPanel( __( 'Stats Settings', 'urban-cms' ), [
          el( ToggleControl, {
            key: 'useCustom',
            label: __( 'Override Customizer stats', 'urban-cms' ),
            checked: useCustom,
            onChange: (v) => setAttributes( { useCustom: v } ),
            help: __( 'When off, values come from Appearance → Customizer → Homepage Stats.', 'urban-cms' ),
          } ),
          ...( useCustom ? fields : [] ),
        ] ),
        el( 'div', bp, serverPreview( 'urban-cms/district-stats', attributes ) )
      );
    },
    save: () => null,
  } );

  /* =====================================================
     2. Events List
     ===================================================== */
  registerBlockType( 'urban-cms/events-list', {
    edit( { attributes, setAttributes } ) {
      const bp = useBlockProps();
      const { count, category, showRsvpBtn, showViewAll } = attributes;

      return el( Fragment, {},
        inspectorPanel( __( 'Events Settings', 'urban-cms' ), [
          el( SelectControl, {
            key: 'count',
            label: __( 'Number of events', 'urban-cms' ),
            value: count,
            options: countOptions,
            onChange: (v) => setAttributes( { count: Number(v) } ),
          } ),
          el( SelectControl, {
            key: 'cat',
            label: __( 'Category filter', 'urban-cms' ),
            value: category,
            options: withAll( data.eventCategories, __( 'All categories', 'urban-cms' ) ),
            onChange: (v) => setAttributes( { category: v } ),
          } ),
          el( ToggleControl, {
            key: 'rsvp',
            label: __( 'Show RSVP button', 'urban-cms' ),
            checked: showRsvpBtn,
            onChange: (v) => setAttributes( { showRsvpBtn: v } ),
          } ),
          el( ToggleControl, {
            key: 'all',
            label: __( 'Show "View All" link', 'urban-cms' ),
            checked: showViewAll,
            onChange: (v) => setAttributes( { showViewAll: v } ),
          } ),
        ] ),
        el( 'div', bp, serverPreview( 'urban-cms/events-list', attributes ) )
      );
    },
    save: () => null,
  } );

  /* =====================================================
     3. Business Directory
     ===================================================== */
  registerBlockType( 'urban-cms/business-directory', {
    edit( { attributes, setAttributes } ) {
      const bp = useBlockProps();
      const { count, category, featuredOnly, layout, showViewAll } = attributes;

      return el( Fragment, {},
        inspectorPanel( __( 'Directory Settings', 'urban-cms' ), [
          el( SelectControl, {
            key: 'count',
            label: __( 'Number of businesses', 'urban-cms' ),
            value: count,
            options: countOptions,
            onChange: (v) => setAttributes( { count: Number(v) } ),
          } ),
          el( SelectControl, {
            key: 'cat',
            label: __( 'Category filter', 'urban-cms' ),
            value: category,
            options: withAll( data.businessCategories, __( 'All categories', 'urban-cms' ) ),
            onChange: (v) => setAttributes( { category: v } ),
          } ),
          el( SelectControl, {
            key: 'layout',
            label: __( 'Layout', 'urban-cms' ),
            value: layout,
            options: [
              { value: 'grid', label: __( 'Grid', 'urban-cms' ) },
              { value: 'list', label: __( 'List', 'urban-cms' ) },
            ],
            onChange: (v) => setAttributes( { layout: v } ),
          } ),
          el( ToggleControl, {
            key: 'feat',
            label: __( 'Featured businesses only', 'urban-cms' ),
            checked: featuredOnly,
            onChange: (v) => setAttributes( { featuredOnly: v } ),
          } ),
          el( ToggleControl, {
            key: 'all',
            label: __( 'Show "View Full Directory" link', 'urban-cms' ),
            checked: showViewAll,
            onChange: (v) => setAttributes( { showViewAll: v } ),
          } ),
        ] ),
        el( 'div', bp, serverPreview( 'urban-cms/business-directory', attributes ) )
      );
    },
    save: () => null,
  } );

  /* =====================================================
     4. Initiative Progress
     ===================================================== */
  registerBlockType( 'urban-cms/initiative-progress', {
    edit( { attributes, setAttributes } ) {
      const bp = useBlockProps();
      const { postId, showProgressBar, showDetails } = attributes;

      const initiativeOptions = withAll(
        data.initiatives.map( (i) => ( { value: i.value, label: i.label } ) ),
        __( '— Select an initiative —', 'urban-cms' )
      );

      return el( Fragment, {},
        inspectorPanel( __( 'Initiative Settings', 'urban-cms' ), [
          el( SelectControl, {
            key: 'post',
            label: __( 'Initiative', 'urban-cms' ),
            value: postId,
            options: initiativeOptions,
            onChange: (v) => setAttributes( { postId: Number(v) } ),
          } ),
          el( ToggleControl, {
            key: 'bar',
            label: __( 'Show progress bar', 'urban-cms' ),
            checked: showProgressBar,
            onChange: (v) => setAttributes( { showProgressBar: v } ),
          } ),
          el( ToggleControl, {
            key: 'det',
            label: __( 'Show budget / lead / target details', 'urban-cms' ),
            checked: showDetails,
            onChange: (v) => setAttributes( { showDetails: v } ),
          } ),
        ] ),
        el( 'div', bp, serverPreview( 'urban-cms/initiative-progress', attributes ) )
      );
    },
    save: () => null,
  } );

  /* =====================================================
     5. Team Grid
     ===================================================== */
  registerBlockType( 'urban-cms/team-grid', {
    edit( { attributes, setAttributes } ) {
      const bp = useBlockProps();
      const { count, columns } = attributes;

      return el( Fragment, {},
        inspectorPanel( __( 'Team Settings', 'urban-cms' ), [
          el( RangeControl, {
            key: 'cols',
            label: __( 'Columns', 'urban-cms' ),
            value: columns,
            min: 2,
            max: 5,
            onChange: (v) => setAttributes( { columns: v } ),
          } ),
          el( SelectControl, {
            key: 'count',
            label: __( 'Max members to show', 'urban-cms' ),
            value: count,
            options: [
              { value: -1, label: __( 'All', 'urban-cms' ) },
              ...countOptions,
            ],
            onChange: (v) => setAttributes( { count: Number(v) } ),
          } ),
        ] ),
        el( 'div', bp, serverPreview( 'urban-cms/team-grid', attributes ) )
      );
    },
    save: () => null,
  } );

  /* =====================================================
     6. Newsletter Signup
     ===================================================== */
  registerBlockType( 'urban-cms/newsletter', {
    edit( { attributes, setAttributes } ) {
      const bp = useBlockProps();
      const { variant, list, source, heading, subheading } = attributes;

      return el( Fragment, {},
        inspectorPanel( __( 'Newsletter Settings', 'urban-cms' ), [
          el( SelectControl, {
            key: 'variant',
            label: __( 'Display variant', 'urban-cms' ),
            value: variant,
            options: [
              { value: 'inline',     label: __( 'Inline (compact)', 'urban-cms' ) },
              { value: 'card',       label: __( 'Card', 'urban-cms' ) },
              { value: 'full-width', label: __( 'Full width (with heading)', 'urban-cms' ) },
            ],
            onChange: (v) => setAttributes( { variant: v } ),
          } ),
          el( TextControl, {
            key: 'list',
            label: __( 'List slug', 'urban-cms' ),
            value: list,
            help: __( 'Subscriber list identifier (e.g. "general", "events-only").', 'urban-cms' ),
            onChange: (v) => setAttributes( { list: v } ),
          } ),
          el( TextControl, {
            key: 'src',
            label: __( 'Source tracking label', 'urban-cms' ),
            value: source,
            onChange: (v) => setAttributes( { source: v } ),
          } ),
          ...( variant === 'full-width' ? [
            el( TextControl, {
              key: 'hd',
              label: __( 'Heading override', 'urban-cms' ),
              value: heading,
              placeholder: __( 'Stay Connected', 'urban-cms' ),
              onChange: (v) => setAttributes( { heading: v } ),
            } ),
            el( TextareaControl, {
              key: 'sub',
              label: __( 'Subheading override', 'urban-cms' ),
              value: subheading,
              onChange: (v) => setAttributes( { subheading: v } ),
            } ),
          ] : [] ),
        ] ),
        el( 'div', bp, serverPreview( 'urban-cms/newsletter', attributes ) )
      );
    },
    save: () => null,
  } );

  /* =====================================================
     7. Hero Banner
     ===================================================== */
  registerBlockType( 'urban-cms/hero-banner', {
    edit( { attributes, setAttributes } ) {
      const bp = useBlockProps( { style: { minHeight: '120px' } } );
      const {
        eyebrow, heading, subheading,
        cta1Text, cta1Url, cta2Text, cta2Url,
        bgColor, accentColor, bgImageUrl, minHeight,
      } = attributes;

      return el( Fragment, {},
        el( InspectorControls, {},
          el( PanelBody, { title: __( 'Content', 'urban-cms' ), initialOpen: true },
            el( TextControl,     { label: __( 'Eyebrow text', 'urban-cms' ), value: eyebrow,    onChange: (v) => setAttributes( { eyebrow: v } ) } ),
            el( TextControl,     { label: __( 'Heading', 'urban-cms' ),       value: heading,    onChange: (v) => setAttributes( { heading: v } ) } ),
            el( TextareaControl, { label: __( 'Subheading', 'urban-cms' ),   value: subheading, onChange: (v) => setAttributes( { subheading: v } ) } ),
          ),
          el( PanelBody, { title: __( 'CTA Buttons', 'urban-cms' ), initialOpen: false },
            el( TextControl, { label: __( 'Primary CTA text', 'urban-cms' ), value: cta1Text, onChange: (v) => setAttributes( { cta1Text: v } ) } ),
            el( TextControl, { label: __( 'Primary CTA URL',  'urban-cms' ), value: cta1Url,  type: 'url', onChange: (v) => setAttributes( { cta1Url: v } ) } ),
            el( TextControl, { label: __( 'Secondary CTA text', 'urban-cms' ), value: cta2Text, onChange: (v) => setAttributes( { cta2Text: v } ) } ),
            el( TextControl, { label: __( 'Secondary CTA URL',  'urban-cms' ), value: cta2Url,  type: 'url', onChange: (v) => setAttributes( { cta2Url: v } ) } ),
          ),
          el( PanelBody, { title: __( 'Background & Style', 'urban-cms' ), initialOpen: false },
            el( 'div', {},
              el( 'p', { style: { fontWeight: 600, marginBottom: '8px' } }, __( 'Background color', 'urban-cms' ) ),
              el( ColorPicker, { color: bgColor,     onChange: (v) => setAttributes( { bgColor: v } ) } ),
            ),
            el( 'div', { style: { marginTop: '16px' } },
              el( 'p', { style: { fontWeight: 600, marginBottom: '8px' } }, __( 'Accent color', 'urban-cms' ) ),
              el( ColorPicker, { color: accentColor, onChange: (v) => setAttributes( { accentColor: v } ) } ),
            ),
            el( TextControl, {
              label: __( 'Background image URL', 'urban-cms' ),
              value: bgImageUrl,
              type: 'url',
              help: __( 'Optional. Use the Media Library URL of a full-width image.', 'urban-cms' ),
              onChange: (v) => setAttributes( { bgImageUrl: v } ),
            } ),
            el( RangeControl, {
              label: __( 'Minimum height (px)', 'urban-cms' ),
              value: minHeight,
              min: 200,
              max: 900,
              step: 40,
              onChange: (v) => setAttributes( { minHeight: v } ),
            } ),
          ),
        ),
        el( 'div', bp, serverPreview( 'urban-cms/hero-banner', attributes ) )
      );
    },
    save: () => null,
  } );

})();
