document.addEventListener('DOMContentLoaded', function () {
    const coutdown = document.querySelector('#lkcountdown')

    if (typeof (prestashop) != 'undefined' && coutdown !== null) {
        const MINUTES = 60
        const HOURS = 60 * MINUTES
        const DAYS = 24 * HOURS
        const $days = document.getElementById('days')
        const elements = {
            hours: document.getElementById('hours'),
            minutes: document.getElementById('minutes'),
            seconds: document.getElementById('seconds')
        }
        if ($days !== null) {
            elements.days = $days
        }

        let previousDiff = {}
        const $body = document.body
        const coutdownAlert = document.querySelector('#countdown-alert')

        function refreshCountDown() {
            const launchDate = Date.parse(coutdown.dataset.time) / 1000
            const difference = launchDate - Date.now() / 1000
            if (difference <= 0) {
                document.location.reload();
            } else {
                const diff = {
                    days: Math.floor(difference / DAYS),
                    hours: Math.floor(difference % DAYS / HOURS),
                    minutes: Math.floor(difference % HOURS / MINUTES),
                    seconds: Math.floor(difference % MINUTES)
                }

                if ($days === null) {
                    diff.hours = Math.floor(diff.hours + (diff.days * 24))
                }

                updateDom(diff)
                window.setTimeout(() => {
                    window.requestAnimationFrame(refreshCountDown)
                }, 1000)
            }
        }

        /**
         *update the dom depending new interva
         * @param{{days:number, hours:number,minutes:number,seconds:number}} diff
         */
        function updateDom(diff) {
            Object.keys(diff).forEach((key) => {
                if (elements[key] !== undefined) {
                    if (previousDiff[key] !== diff[key]) {
                        elements[key].innerText = diff[key]
                    }
                }
            })
            previousDiff = diff
        }
        refreshCountDown()
    }
});