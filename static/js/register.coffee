$ = jQuery
$ ->
$(document).ready ($) ->
  newNum = 1
  newElem = $("div.su_prototype").clone().attr("id", "row" + newNum).addClass("clonedInput").removeClass("su_prototype")
  $("form.formClass").prepend newElem
  $("#btnDel").prop "disabled", true
  $("#btnAdd").click ->
    num = $(".clonedInput").length # how many "duplicatable" input fields we currently have
    newNum = new Number(num + 1) # the numeric ID of the new input field being added
    newElem = $("div.su_prototype").clone().attr("id", "row" + newNum).addClass("clonedInput").removeClass("su_prototype")
    newElem.find("input").each ->
      name = $(this).attr("name")
      name = name.slice(11, -1)
      $(this).attr "name", "results[" + newNum + "][" + name + "]"

    newElem.find("select").each ->
      name = $(this).attr("name")
      name = name.slice(11, -1)
      $(this).attr "name", "results[" + newNum + "][" + name + "]"

    
    # insert the new element after the last "duplicatable" input field
    $("#row" + num).after newElem
    $("#btnDel").prop "disabled", false
    

  $("#btnDel").click ->
    num = $(".clonedInput").length # how many "duplicatable" input fields we currently have
    $("#row" + num).remove() # remove the last element
    
    # enable the "add" button
    # $("#btnAdd").attr "disabled", ""
    
    # if only one element remains, disable the "remove" button
    $("#btnDel").prop "disabled", true  if num - 1 is 1