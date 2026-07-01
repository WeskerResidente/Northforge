const hero = document.querySelector('.home-hero');

if (hero) {
	const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	let targetProgress = 0;
	let currentProgress = 0;
	let animationFrameId = null;

	const getHeroProgress = () => {
		const scrollPosition = window.scrollY || window.pageYOffset || 0;
		const heroRect = hero.getBoundingClientRect();
		const heroTop = scrollPosition + heroRect.top;
		const heroHeight = heroRect.height || hero.offsetHeight || 1;

		return Math.min(Math.max((scrollPosition - heroTop) / heroHeight, 0), 1);
	};

	const renderHeroMotion = () => {
		currentProgress += (targetProgress - currentProgress) * 0.08;

		hero.style.setProperty('--truck-drift', `${currentProgress * 90}px`);
		hero.style.setProperty('--pallet-drift', `${currentProgress * -70}px`);
		hero.style.setProperty('--motion-rotate', `${currentProgress * 10}deg`);
		hero.style.setProperty('--motion-opacity', `${1 - currentProgress * 0.22}`);

		if (Math.abs(targetProgress - currentProgress) > 0.001) {
			animationFrameId = window.requestAnimationFrame(renderHeroMotion);
			return;
		}

		currentProgress = targetProgress;
		animationFrameId = null;
	};

	const updateHeroMotion = () => {
		targetProgress = getHeroProgress();

		if (animationFrameId === null) {
			animationFrameId = window.requestAnimationFrame(renderHeroMotion);
		}
	};

	if (!prefersReducedMotion) {
		targetProgress = getHeroProgress();
		currentProgress = targetProgress;
		updateHeroMotion();

		window.addEventListener('scroll', updateHeroMotion, { passive: true });
		window.addEventListener('resize', updateHeroMotion);
	}
}
