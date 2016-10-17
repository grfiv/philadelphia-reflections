<!-- comment-entry form -->
                <script src="../js/comment_entry_form.js"></script>

                <div style='background:#fff;margin:0 auto;text-align:left;width:760px;padding:10px 10px 10px 10px;border:2px solid black;'>
                    <h2 style='background-color:#22007d;color:#ea9f20;margin:0;padding:10px 10px 10px 10px;text-align:center;font-size:xx-large;'>Please Let Us Know What You Think</h2>
                    <form id='comment_form'
                          method='post'
                          action='http://www.philadelphia-reflections.com/comment_insert_direct.php'
                          target='comment_window'
                          style='width:735px;font-family:"Lucida Grande", Tahoma, Arial, Verdana, sans-serif;font-size:small;border:2px solid black;padding:10px;margin-top:0;'
                          onsubmit='return validateForm()' >
                        <table>
                            <tr><td><label for='name'>Name or nickname</label></td>
                                <td><input id='name' name='name'   type='text' maxlength='255' size='50' style='border:1px solid black;' /></td>
                            </tr>
                            <tr><td><label for='email'>Email </label></td>
                                <td><input id='email' name='email' type='text' maxlength='255' size='50' style='border:1px solid black;' /></td>
                            </tr>
                            <tr><td><label for='comment'>Comment</label></td>
                                <td><textarea id='comment' name='comment' rows='20' cols='70' style='border:1px solid black;'></textarea>
                                    <input type='hidden' name='type' id='type' value='{{comment_type}}' />
                                    <input type='hidden' name='key'  id='key'  value='{{comment_key}}' /><br />
                                    <span style='font-size:small'>(HTML tags provide better formatting)</span></td>
                            </tr>
                            <tr><td colspan='2'><input id='saveForm' type='submit' value='Click to Send Us Your Comment' /></td>
                            </tr>
                        </table>
                    </form>
                    <p style='font-size:small;margin-top:5px;'>Because of robot spam we ask you to confirm your comment:
                    we will send you an email containing a link to click.
                    We apologize for this inconvenience but this ensures the quality of the comments. (Your email will not be displayed.)<br />Thank you.</p>
                </div>
