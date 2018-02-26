/**
 * Created by Vincent on 31/03/2017.
 */

function Panel (course_id, room_id, color, instructor, time, id)
{
    this.Panel = createPanel(id);
    this.PanelHeading = createPanelHeading(course_id, color, id);
    this.PanelBody = createPanelBody(room_id, time);
    this.Panel.append(this.PanelHeading);
    this.Panel.append(this.PanelBody);
    return this.Panel;
/*
    this.init = function () {
        this.Panel.append(this.PanelHeading);
        this.Panel.append(this.PanelBody);
    };
    this.get = function () {
        return this.Panel;
    };*/
}

function createPanel(id)
{
    var Panel= document.createElement('DIV');
    Panel.className=['panel panel-default'];
    Panel.id = id;

    return Panel;
}

function createPanelHeading(course_id, color, id)
{
    var PanelHeading=document.createElement('DIV');
    PanelHeading.className='panel-heading';
    PanelHeading.id = id;
    PanelHeading.style.setProperty('background-color', color, "important");
/*
    var colorBlock = document.createElement('DIV');
    colorBlock.className='colorblock';
    colorBlock.style.backgroundColor = color;
    PanelHeading.append(colorBlock);*/
    PanelHeading.append(document.createElement('P').appendChild(document.createTextNode(course_id)));
    return PanelHeading;
}

function createPanelBody(room_id, time)
{
    var PanelBody = document.createElement('DIV');
    PanelBody.className=['panel-body'];
    //PanelBody.append(document.createElement('BR'));
    var timeParagraph = document.createElement('P')
    var timeText = document.createTextNode("Time: " + time)
    timeParagraph.appendChild(timeText)
    PanelBody.appendChild(timeParagraph)
    var roomParagraph = document.createElement('P')
    var roomText = document.createTextNode("Room: " + room_id)
    roomParagraph.appendChild(roomText)
    PanelBody.appendChild(roomParagraph)
    /* PanelBody.append(document.createElement('P').appendChild(document.createTextNode("Time: " + time)));
    PanelBody.append(document.createElement('BR'));
    PanelBody.append(document.createElement('P').appendChild(document.createTextNode("Room: " + room_id))); */
    return PanelBody;
}
