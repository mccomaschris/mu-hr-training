module.exports = {
  presets: [
    require('@marshallu/marsha-tailwind')
  ],
	purge: {
		content: [
			'./source/css/*.css',
			'./source/css/*/*.css',
			'./*.php',
		],
	}
}
