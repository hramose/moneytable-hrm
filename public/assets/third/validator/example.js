$(document).ready(function() {
	
    $('#department-form').bootstrapValidator({
        message: 'This value is not valid!!',
        fields: {
            department_name: {
                message: 'Please enter name!!',
                validators: {
                    notEmpty: {
                        message: 'Name is required and can\'t be empty!!'
                    }
                }
            }
        }
    });
	
});