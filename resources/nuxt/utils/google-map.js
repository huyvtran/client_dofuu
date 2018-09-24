export function getLocation(value) {
	var vm = this
	return new Promise((resolve, reject) => {
		var geocoder = new google.maps.Geocoder();
		geocoder.geocode({'address': value}, function(response, status) {
			if(status === 'OK') {
				resolve(response)
			} else {
				reject('Geocode was not successful for the following reason: ' + status)
			}
		})
	})
}

export function geocoder(type, location) {
	return new Promise((resolve, reject) => {
		var geocoder = new google.maps.Geocoder()
		if(type === 'latlng') {
			geocoder.geocode({'location': {lat: location.lat, lng: location.lng}}, function(results, status) {
				if(status === 'OK') {
					resolve(results)
				}
			})
		} else {
			geocoder.geocode({address: location}, function(results, status) {
				if(status == 'OK') {
					resolve(results)
				}
			})
		}
	})
}

export function distanceMatrixService(origin, destination) {
	var start           = new google.maps.LatLng(origin.lat, origin.lng);
	var end             = new google.maps.LatLng(destination.lat, destination.lng);
	var service         = new google.maps.DistanceMatrixService()
	var originList      = []
	var destinationList = []
	var distance        = '0 Km'
	var duration        = '0 phút'
	return new Promise((resolve, reject) => {
		service.getDistanceMatrix({
			origins: [start],
			destinations: [end],
			travelMode: 'DRIVING'
		}, function(res, status) {
			if (status === 'OK') {
				originList      = res.originAddresses
				destinationList = res.destinationAddresses
				for (var i = 0; i < originList.length; i++) {
					var results = res.rows[i].elements
					for (var j = 0; j < results.length; j++) {
						var element = results[j]
						distance    = element.distance.text
						duration    = element.duration.text
					}
					resolve({'distance': distance,'duration': duration})
				}
			}					
		})
	})
}

export function typeIcon(value) {
	var status = new String(value).toLowerCase()
	switch(status) {
		case 'quán ăn':
		return {url: '/img/qa_blue.png'}
		break
		case 'trà sữa':
		return {url: '/img/ts_blue.png'}
		break
		case 'cà phê':
		return {url: '/img/cp_blue.png'}
		break
		case 'ăn vặt':
		return {url: '/img/av_blue.png'}
		break
		case 'thức ăn nhanh':
		return {url: '/img/ff_blue.png'}
		break
		case 'vỉa hè':
		return {url: '/img/vh_blue.png'}
		break
	}
}