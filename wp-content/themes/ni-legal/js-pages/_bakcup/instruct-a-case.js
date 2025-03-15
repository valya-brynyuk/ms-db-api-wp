/*------------------------------------------------------------------------------------*\
	SORTING API TABLE
\*------------------------------------------------------------------------------------*/

var listOptions2 = {
    valueNames: [  'applicant','quote-date','instructed']
};

var userList2 = new List('sortable-bs-2', listOptions2);

// userList2.sort("quote-date", {
//     order: "desc"
// })

//check if filter-100-percent checkbox is checked, if so, filter 100% lines
if (document.getElementById('filter-instructed').checked) {
	userList2.filter(function(item) {
		if (item.values().instructed == '1') {
		   return false;
		} else {
		   return true;
		}
	}); // Only items with progress != '100%' are shown in list
	userList2.sort("quote-date", {
		order: "desc"
	})
}
else {
	userList2.sort("quote-date", {
		order: "desc"
	})
	// userList2.filter();
	// userList2.sort("quote-date", {
	// 	order: "desc"
	// })
}

jQuery('#filter-instructed').click(function(){
	if (jQuery(this).is(':checked')) {
		userList2.filter(function(item) {
			if (item.values().instructed == '1') {
			   return false;
			} else {
			   return true;
			}
		}); // Only items with progress != '100%' are shown in list
		userList2.sort("quote-date", {
			order: "desc"
		})
	}
	else {
		userList2.filter();
		userList2.sort("quote-date", {
			order: "desc"
		})
	}
});