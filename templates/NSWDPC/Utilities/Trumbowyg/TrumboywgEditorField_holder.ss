<div id="$HolderID" class="field<% if $extraClass %> $extraClass<% end_if %>">
    <% if $Title %><label class="left" for="$ID">$Title</label><% end_if %>
    <% if $Message %><span class="message $MessageType">$Message</p><% end_if %>
    <% if $Description %><p class="description">$Description</p><% end_if %>
    <div class="middleColumn">
        $Field
    </div>
    <% if $RightTitle %><label class="right" for="$ID">$RightTitle</label><% end_if %>
</div>
