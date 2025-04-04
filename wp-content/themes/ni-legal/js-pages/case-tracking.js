/*------------------------------------------------------------------------------------*\
	SORTING API TABLE
\*------------------------------------------------------------------------------------*/

var listOptions = {
    valueNames: [ 'client','progresshidden','cancelledhidden','matter-opened-date','case-details', 'MatterCompleted', 'MatterCancelled' ]
};

var userList = new List('sortable-bs', listOptions);

// userList.sort("matter-opened-date", {
//     order: "asc"
// })
//check if filter-100-percent checkbox is checked, if so, filter 100% lines
// if (document.getElementById('filter-new').value == 'completed') {
// 	console.log('completed:');
// 	userList.filter(function(item) {
// 		// console.log('filtering completed!');
// 		tdValueProgress = item.values().progresshidden;
// 		tdValueProgress = tdValueProgress.trim();
// 		// console.log(tdValueProgress);
//
// 		if (tdValueProgress == '100%') {
// 		   return true;
// 		} else {
// 		   return false;
// 		}
// 	}); // Only items with progress != '100%' are shown in list
// 	userList.sort("matter-opened-date", {
// 		order: "asc"
// 	})
// }
// else if (document.getElementById('filter-new').value == 'cancelled') {
// 	console.log('cancelled:');
// 	userList.filter(function(item) {
// 		// console.log('filtering cancelled!');
// 		tdValueCancelled = item.values().cancelledhidden
// 		tdValueCancelled = tdValueCancelled.trim();
// 			// console.log(tdValue);
// 		if (tdValueCancelled == null || tdValueCancelled == '' || tdValueCancelled == ' ') {
// 		   return true;
// 		} else {
// 		   return false;
// 		}
// 	}); // Only items with progress != '100%' are shown in list
// 	userList.sort("matter-opened-date", {
// 		order: "asc"
// 	})
// }
// else if (document.getElementById('filter-new').value == 'active') {
// 	// console.log('active:');
// 	userList.filter(function(item) {
// 		// console.log('filtering active!');
// 		tdValueCancelled = item.values().cancelledhidden;
// 		tdValueCancelled = tdValueCancelled.trim();
// 		tdValueProgress = item.values().progresshidden;
// 		tdValueProgress = tdValueProgress.trim();
// 		if (((tdValueCancelled == null || tdValueCancelled == '' || tdValueCancelled == ' ') ) && ( tdValueProgress != '100%')) {
// 		   return true;
// 		} else {
// 		   return false;
// 		}
// 	}); // Only items with progress != '100%' are shown in list
// 	userList.sort("matter-opened-date", {
// 		order: "asc"
// 	})
// }
// else {
// 	userList.sort("matter-opened-date", {
// 		order: "asc"
// 	})
// 	// userList.filter();
// 	// userList.sort("matter-opened-date", {
// 	// 	order: "asc"
// 	// })
// }

// jQuery('#filter-100-percent').click(function(){
// 	if (jQuery(this).is(':checked')) {
// 		userList.filter(function(item) {
// 			if (item.values().progress == '100%') {
// 			   return false;
// 			} else {
// 			   return true;
// 			}
// 		}); // Only items with progress != '100%' are shown in list
// 		userList.sort("matter-opened-date", {
// 			order: "asc"
// 		})
// 	}
// 	else {
// 		userList.filter();
// 		userList.sort("matter-opened-date", {
// 			order: "asc"
// 		})
// 	}
// });

const $filterNew = jQuery('#filter-new');
$filterNew.change(function () {
    let selection = this.value;
    if (selection === 'completed') {
		userList.filter(function(item) {
			const values = item.values();
			const completedDate = values.MatterCompleted.trim();

			return !!completedDate.length;
		}); // Only items with progress != '100%' are shown in list
		userList.sort("matter-opened-date", {
			order: "asc"
		})
    } 
	else if (selection === 'cancelled') {
		userList.filter(function(item) {
			const values = item.values();
			const cancelledDate = values.MatterCancelled.trim();

			return !!cancelledDate.length;
		}); // Only items with progress != '100%' are shown in list
		userList.sort("matter-opened-date", {
			order: "asc"
		})
	}
	else if (selection === 'active'){
		userList.filter(function(item) {
			const values = item.values();
			const completedDate = values.MatterCompleted.trim();
			const cancelledDate = values.MatterCancelled.trim();

			return !completedDate.length && !cancelledDate.length;
		}); // Only items with progress != '100%' are shown in list
		userList.sort("matter-opened-date", {
			order: "asc"
		})
	}
	else {
        userList.filter();
		userList.sort("matter-opened-date", {
			order: "asc"
		})
    }
});

$filterNew.trigger('change');