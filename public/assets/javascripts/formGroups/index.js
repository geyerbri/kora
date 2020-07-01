var Kora = Kora || {};
Kora.FormGroups = Kora.FormGroups || {};

Kora.FormGroups.Index = function() {
  var self = Kora.FormGroups.Index;


  /**
   * Remove a user from a form's form group.
   *
   * @param formGroup {int} The form group id.
   * @param userId {int} The user id.
   * @param fid {int} The form id.
   */
  self.removeUser = function(formGroup, userID, fid) {
    $.ajax({
      url: removeUserPath,
      type: 'POST',
      data: {
        "_token": CSRFToken,
        "_method": 'patch',
        "userId": userID,
        "formGroup": formGroup,
        "fid": fid
      },
      success: function() {
        $('.note').children('p').text('User Successfully Removed from Group');
        $('.notification').removeClass('dismiss');
        setTimeout(function(){
          $('.notification').addClass('dismiss');
        }, 4000);
        var $user = $("#list-element" + formGroup + userID);
        var $parent = $user.parent();
        var userName = $user.children('.view-user-js').html();

        $user.fadeOut(function() {
          // Remove the user from the list of users currently in the group.
          $(this).remove();

          // If was the last user of the group display no-users text

          if ($parent.children('.user-js').length == 0) {
            self.showNoUsersText($parent, formGroup);
          }
        });

        // Add the user to the users that can be added to the group.
        var option = '<option value="' + userID + '">' + userName + '</option>';
        var $select = $('#select-' + formGroup);
        var canAddToSelect = true;

        $select.children('option').each(function() {
          if ($(this).val() == userID) {
            canAddToSelect = false;
            return;
          }
        });

        if (canAddToSelect) {
          $select.append(option);
        }

        // Close the modal.
        Kora.Modal.close();
      }
    });
  }


  /**
   * Helper function for displaying a user in the form group.
   *
   * @param isUserMove {int} Whether or not the user was moved from a group
   * @param fid {int} The form id
   * @param userIDs {array} The list of userIDs
   * @param userMap {object} The userMap for the function.
   * @param $groupCard {jQuery} The parent element to append user.
   */
  self.showUser = function(isUserMove, fid, formGroup, userIDs, userMap, $groupCard) {
    // Add the user to the users currently in the group.
    for (userID of userIDs) {
      if (isUserMove.length > 0) {
        var element = '<div style="display:none" class="user user-js" ';
        element += 'id="list-element' + formGroup + userID + '">';
        element += userMap[userID];
        element += '</div>';
        $groupCard.append(element).children('.user-js').fadeIn();
      } else {
        var element = '<div style="display:none" class="user user-js" ';
        element += 'id="list-element' + formGroup + userID;
        element += '"><a href="#" class="name view-user-js">' + userMap[userID] + '</a>';
        element += '<a href="#" class="cancel remove-user-js" data-value="[';
        element += formGroup + ", " + userID + ", " + fid + ']">';
        element += '<i class="icon icon-cancel"></i></a></div>';
        $groupCard.append(element).children('.user-js').fadeIn();
      }
    }
  }

  self.showNoUsersText = function($groupCard, groupID) {
    // Is the last user of the group so display no-users text
    var element = '<p style="display: none" class="no-users no-users-js">';
    element += '<span>No users in this group, select</span><a href="#" class="user-add ';
    element += 'add-users-js underline-middle-hover" data-select="add_user_select' + groupID;
    element += '" data-group="' + groupID + '">';
    element += '<i class="icon icon-user-add"></i><span>Add User(s) to Group</span></a>';
    element += '<span>to add some!</span></p>';
    $groupCard.append(element).children('.no-users-js').fadeIn();
  }

  /**
   * Add users to a form's form group.
   *
   * @param formGroup {int} The form group id.
   * @param userIDs {array} The array of user ids.
   * @param $select {jQuery} The selector for removing.
   */
  self.addUsers = function(formGroup, userIDs, $select) {

    $.ajax({
      url: addUsersPath,
      type: 'POST',
      data: {
        "_token": CSRFToken,
        "_method": 'patch',
        "userIDs": userIDs,
        "formGroup": formGroup
      },
      success: function(data) {
        $('.note').children('p').text('User(s) Successfully Added to Permissions Group!');
        $('.notification').removeClass('dismiss');
        setTimeout(function(){
          $('.notification').addClass('dismiss');
        }, 4000);
        // data is supposed to be the Old Group ID
        var userMap = {} // A map of userID to their content
        for (userID of userIDs) {
          var userContent = $('#list-element' + data + userID).html();
          userMap[userID] = userContent;
        }

        $('.multi-select').each(function(index) {
          var $this = $(this);
          var groupID = $this.data('group');

          if (typeof groupID == 'undefined') {
            return true;
          } else if (groupID == formGroup) {
            $this.find('option').each(function() {

              // Remove from select if added to formGroup
              var val = $(this).attr('value');
              if (userIDs.includes(val)) {
                if (data.length == 0) {
                  userMap[val] = $(this).html(); // We need the name for later.
                }

                $(this).remove();
              }
            });
          } else {
            // this select needs to have options added
            for (userID of userIDs) {
              var option = '<option value="' + userID + '">' + userMap[userID] + '</option>';

              // check if if is already in this select list
              var canAddToSelect = true;
              $this.children('option').each(function() {
                if ($(this).val() == userID) {
                  canAddToSelect = false;
                  return;
                }
              });

              if (canAddToSelect) {
                $this.append(option);
              }
            }
          }
        });

        $('.group-js').each(function() {
          var $this = $(this);
          var $groupCard = $('#' + $this.attr('id') + " .users-js");
          var groupID = $groupCard.data('group');
          var userContent = $('#list-element' + formGroup + userID).html();

          if ($this.attr('id') == formGroup) {
            // remove no-users p if exists
            var $noUsers = $groupCard.children(".no-users-js");
            if ($noUsers.length > 0) {
              $noUsers.fadeOut(function() {
                $(this).remove();
                self.showUser(data, pid, formGroup, userIDs, userMap, $groupCard);
              })
            } else {
              self.showUser(data, pid, formGroup, userIDs, userMap, $groupCard);
            }
          } else {
            // Remove the user from the users currently in the group.
            usersInGroup = $groupCard.children('.user-js').length
            for (userID of userIDs) {
              $elementToRemove = $groupCard.find('#list-element' + $this.attr('id') + userID);

              if ($elementToRemove.length && usersInGroup == 1) {
                $elementToRemove.fadeOut(function() {
                  // Remove the user from the list of users currently in the group.
                  $(this).remove();
                  self.showNoUsersText($groupCard, groupID);
                });
              } else if ($elementToRemove.length) {
                $elementToRemove.fadeOut(function() {
                  // Remove the user from the list of users currently in the group.
                  $(this).remove();
                });
              }
            }
          }
          initializeViewUserModal();

        });
      }
    });
  }

  /**
   * Edit form group name.
   *
   * @param gid {int} The form group id.
   * @param newName {string} The new name of the group.
   */
  self.editGroupName = function(gid, newName) {
    if (newName == '') {
      // Validation: no blank name
      $('.edit-group-name-button-js input').prop('disabled', true);
      return;
    } else {
      $.ajax({
        url: editNamePath,
        type: 'POST',
        data: {
          "_token": CSRFToken,
          "_method": 'patch',
          "gid": gid,
          "name": newName
        },
        success: function(response) {
          $('#' + gid).find('.name-js').html(newName);
          Kora.Modal.close();
        }
      });
    }
  }

  /**
   * Edit form group name.
   *
   * @param gid {int} The form group id
   */
  self.deletePermissionsGroup = function(gid) {
    $.ajax({
      url: deletePermissionsPath,
      type: 'POST',
      data: {
        "_token": CSRFToken,
        "_method": 'delete',
        "formGroup": gid
      },
      success: function() {
        Kora.Modal.close();

        // Allow for Modal to close before page reload.
        setTimeout(function() {
          location.reload();
        }, 500);
      }
    });
  }

  /**
   * Update the permissions of a particular form group.
   *
   * @param formGroup {int} The form group id
   */
  self.updatePermissions = function(formGroup) {
    // If the box is checked, allow users in the form group to do that action within the form.
    var permCreate = ($('#create-' + formGroup).is(':checked') ? 1 : 0);
    var permEdit = ($('#edit-' + formGroup).is(':checked') ? 1 : 0);
    var permDelete = ($('#delete-' + formGroup).is(':checked') ? 1 : 0);
    var permIngest = ($('#ingest-' + formGroup).is(':checked') ? 1 : 0);
    var permModify = ($('#modify-' + formGroup).is(':checked') ? 1 : 0);
    var permDestroy = ($('#destroy-' + formGroup).is(':checked') ? 1 : 0);

    $.ajax({
      url: updatePermissionsPath,
      type: 'POST',
      data: {
        "_token": CSRFToken,
        "_method": 'patch',
        "formGroup": formGroup,
        "permCreate": permCreate,
        "permEdit": permEdit,
        "permDelete": permDelete,
        "permIngest": permIngest,
        "permModify": permModify,
        "permDestroy": permDestroy
      }
    });
  }

  function initializePermissionsToggles() {



    $('.permission-toggle-by-name-js').click(function(e) {
      e.preventDefault();

      var $this = $(this);
      var $cardToggle = $this.parent().next();
      $cardToggle.children().click()
    });

    $('.permission-toggle-js').click(function(e) {
      e.preventDefault();

      var $this = $(this);
      var $header = $this.parent().parent();
      var $form = $header.parent();
      var $content = $header.next();

      $this.children().toggleClass('active');
      $form.toggleClass('active');
      if ($form.hasClass('active')) {
        $header.addClass('active');
        $form.animate({
          height: $form.height() + $content.outerHeight(true) + 'px'
        }, 230);
        $content.effect('slide', {
          direction: 'up',
          mode: 'show',
          duration: 240
        });
      } else {
        $form.animate({
          height: '58px'
        }, 230, function() {
          $header.hasClass('active') ? $header.removeClass('active') : null;
          $content.hasClass('active') ? $content.removeClass('active') : null;
        });
        $content.effect('slide', {
          direction: 'up',
          mode: 'hide',
          duration: 240
        });
      }

    });
  }

  function initializeNewPermissionModal() {
    $('.new-permission-js').click(function(e) {
      e.preventDefault();

      $newPermissionsModal = $('.new-permission-modal-js');
      $newPermissionsModal.find('.multi-select').chosen({
        width: '100%',
      });

	  var inputDef = $('.chosen-container').children('.chosen-choices');

	  inputDef.on('click', function() {
		  if (inputDef.siblings('.chosen-drop').children('.chosen-results').children().length === 0) {
			  inputDef.siblings('.chosen-drop').children('.chosen-results').append('<li class="no-results">No options to select!</li>');
		  } else if (inputDef.siblings('.chosen-drop').children('.chosen-results').children('.active-result').length === 0) {
			  inputDef.siblings('.chosen-drop').children('.chosen-results').append('<li class="no-results">No more options to select!</li>');
		  }
	  });

      Kora.Modal.open($newPermissionsModal);
    });
  }

  function initializeEditGroupNameModal() {
    $('.edit-group-name-js').click(function(e) {
      e.preventDefault();

      // Initialization of Modal with Name editable
      var groupName = $(this).data('name');
      var gid = $(this).data('group');

      var $editNameModal = $('.edit-group-name-modal-js');
      var $editNameModalInput = $editNameModal.find('.group-name-js');
      $editNameModalInput.val(groupName);

      // Submission of Editing a Name
      var submitNameChange = function(gid) {
        return function(e) {
          e.preventDefault();

          var groupName = $('.edit-group-name-modal-js').find('.group-name-js').val();
          self.editGroupName(gid, groupName);
        }
      }
      $('.edit-group-name-submit-js').on('click', submitNameChange(gid));

      Kora.Modal.open($('.edit-group-name-modal-js'));
    });
  }

  function initializeAddUsersModal() {
    $(document).on('click', '.add-users-js', function(e) {
      e.preventDefault();

      // Initialization of Modal with Users selectable
      var selectID = $(this).data('select');
      var groupID = $(this).data('group');
      var $select = $("#" + selectID);
      var $addUserModal = $('.add-users-modal-js');
      var $addUserModalBody = $addUserModal.find('.body');
      $addUserModalBody.html($select.html());

      $addUserModalBody.find('.multi-select').chosen({
        width: '100%',
      });

	  var inputDef = $('.chosen-container').children('.chosen-choices');

	  inputDef.on('click', function() {
		  if (inputDef.siblings('.chosen-drop').children('.chosen-results').children().length === 0) {
			  inputDef.siblings('.chosen-drop').children('.chosen-results').append('<li class="no-results">No options to select!</li>');
		  } else if (inputDef.siblings('.chosen-drop').children('.chosen-results').children('.active-result').length === 0) {
			  inputDef.siblings('.chosen-drop').children('.chosen-results').append('<li class="no-results">No more options to select!</li>');
		  }
	  });

      // Submission of Adding a User
      var submitUsers = function(groupID, $addUserModal, $select) {
        return function(e) {
          e.preventDefault();

          values = $("#select-" + groupID).chosen().val();

          // Validation: at least one selected
          if (values != null) {
            self.addUsers(groupID, values, $select);
            Kora.Modal.close($addUserModal);

            // Kill the chosen element after Modal Close.
            setTimeout(function() {
              $(".multi-select").chosen('destroy');
              $addUserModal.find('.body').html('');
            }, 500);
          }
        };
      }
      $('.add-users-submit-js').on('click', submitUsers(groupID, $addUserModal, $select));

      Kora.Modal.open($addUserModal);
    });
  }

  function initializeRemoveUserModal() {
    $(document).on('click', '.remove-user-js', function(e) {
      e.preventDefault();

      var data = $(this).data('value');
      var removeUser = function(e) {
        e.preventDefault();
        self.removeUser(data[0], data[1], data[2]);
      };

      $('.user-remove-submit-js').on('click', '.user-remove-btn-js', removeUser);
      Kora.Modal.open($('.remove-user-modal-js'));
    });
  }

  function initializeViewUserModal() {
    $('.view-user-js').click(function(e) {
      e.preventDefault();

      $this = $(this);
      // Check if profile picture exists
      $modal.find('.profile-js').html("").css("top", "-63px");
      $.get($this.data('profile'))
          .done(function() {
            $modal.find('.profile-js').html('<img src="' + $this.data('profile') + '" alt="Profile Pic">');
          })
          .fail(function() {
            $modal.find('.profile-js').html('<i class="icon icon-user">').css("top", "-23px");;
          });
      $modal.find('.name-attr-js').html($this.data('name'));
      $modal.find('.username-attr-js').html($this.data('username'));
      $modal.find('.email-attr-js').html($this.data('email'));
      $modal.find('.organization-attr-js').html($this.data('organization'));
      $modal.find('.profile-link-js').attr('href', $this.data('profile-url'));

      Kora.Modal.open($('.user-profile-modal-js'));
    });
  }

  function initializeDeletePermissionModal() {
    $('.delete-permission-group-js').click(function(e) {
      e.preventDefault();

      Kora.Modal.open($('.delete-permission-group-modal-js'));

      var gid = $(this).data('group');
      var deletePermissionsGroup = function(gid) {
        return function(e) {
          e.preventDefault();
          self.deletePermissionsGroup(gid);
        }
      };

      $('.permissions-delete-submit-js').on('click', '.permissions-delete-btn-js', deletePermissionsGroup(gid));
      Kora.Modal.open($('.delete-permission-group-modal-js'));
    });
  }

  function initializeValidation() {
	var checkbox_names = {"create": true, "ingest": true, "edit": true, "modify": true,
		"delete": true, "destroy": true};

	function error(input, error_message) {
	  $(input).prev().text(error_message);
	  $(input).addClass("error"); // applies the error border styling
	}

	function success(input) { // when validation is passed on an input
	  $(input).prev().text("");
	  $(input).removeClass("error");
	}

	function validateGroupName() {
	  var name_input = $(".create-group-name-js");
	  var name = name_input.val();

	  if (name == null || name == "") {
		error(name_input, "This field is required");
		return false;
	  } else {
		success(name_input);
		return true;
	  }
	}

	function validateGroupOptions() {
	  var check_create = $("input[name='create'].check-box-input");
	  var check_ingest = $("input[name='ingest'].check-box-input");
	  var check_edit = $("input[name='edit'].check-box-input");
	  var check_modify = $("input[name='modify'].check-box-input");
	  var check_delete = $("input[name='delete'].check-box-input");
	  var check_destroy = $("input[name='destroy'].check-box-input");
	  var error_msg = $(".group-options-error-message");

	  if (check_create && check_edit && check_delete &&
	  (check_create.prop("checked") || check_ingest.prop("checked") || check_edit.prop("checked")
	  || check_modify.prop("checked") || check_delete.prop("checked") || check_destroy.prop("checked"))) {
		error_msg.text("");
		return true;
	  } else {
	    error_msg.text("Select at least one permission");
		return false;
	  }
	}

    $(".create-group-name-js").blur(function() {
	  validateGroupName();
    });

	$(".check-box-input").click(function() {
	  var name = $(this).attr("name");
	  if (name !== null && checkbox_names[name] != null) {
	    validateGroupOptions();
	  }
	});

	$(".create-submit-js").click(function(e) {
	  var valid_name = validateGroupName();
	  var valid_options = validateGroupOptions();

	  if (!valid_name || !valid_options) {
		e.preventDefault();
	  } else {
		display_loader();
	  }
	});
  }

  Kora.Modal.initialize();
  initializePermissionsToggles();
  initializeNewPermissionModal();
  initializeDeletePermissionModal();
  initializeEditGroupNameModal();
  initializeAddUsersModal();
  initializeRemoveUserModal();
  initializeViewUserModal();
  initializeValidation();
}
