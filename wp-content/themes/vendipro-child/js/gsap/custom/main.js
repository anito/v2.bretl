var rotation = document.querySelector("#rotational"),
        linear = document.querySelector("#linear"),
        showHandles = document.querySelector("#showHandles"),
        origin = document.querySelector("#origin"),
        originContainer = document.querySelector("#originContainer"),
        ghost = document.querySelector("#ghost"),
        devTools, tracer, tl;

TweenLite.defaultEase = Power1.easeInOut;
ghost.setAttribute("d", document.querySelector("#svg_start_object").getAttribute("d"));

rotation.addEventListener("change", start);
linear.addEventListener("change", start);
showHandles.addEventListener("change", start);
origin.addEventListener("change", start);

function start(paused) {
    if (tracer) {
        for (var i = 0; i < tracer.length; i++) {
            tracer[i].kill();
        }
    }
    if (devTools) {
        devTools.kill();
    }
    if (tl) {
        tl.progress(0).kill();
    }
    var type = rotation.checked ? "rotational" : "linear";
    origin.disabled = !rotation.checked;
    originContainer.style.opacity = rotation.checked ? "1" : "0.3";
    tl = new TimelineMax({repeat: -1, yoyo: true, repeatDelay: 1.5, delay: 1, id: "morphing", paused: (paused === true)});
    var tween = TweenLite.to("#svg_start_object", 3, {morphSVG: {shape: "#svg_object_2", type: type, origin: origin.value}});
    tl.add(tween);
    ghost.style.visibility = rotation.checked ? "visible" : "hidden";
    if (rotation.checked) {
        tl.to(ghost, 3, {morphSVG: "#svg_object_2"}, 0);
    }
    if (showHandles.checked) {
        tracer = MorphTracer.create(tween, {precision: 0.5, lineOpacity: 0.4, lineColor: "#886ead", originColor: "#9bea00"});
    }
    devTools = GSDevTools.create({animation: tl, globalSync: false, loop: true});
}


var showing = true;
document.querySelector("#svg").addEventListener("click", function () {
    showing = !showing;
    TweenLite.set(".controls, h1", {visibility: showing ? "visible" : "hidden"});
    tl.play();
});




//----- MorphTracer helper class ---------------

function MorphTracer(tween, v) {
    var _defaults = function (o, d) {
        for (var p in d) {
            if (!(p in o)) {
                o[p] = d[p];
            }
        }
        return o;
    },
            vars = _defaults(v || {}, {anchorSize: 1.5, originSize: 3, originColor: "red", anchorColor: "white", lineWidth: 1, lineColor: "white", lineOpacity: 0.15, controlPoints: false, precision: 1}),
            _cos = Math.cos,
            _sin = Math.sin,
            _atan2 = Math.atan2,
            _sqrt = Math.sqrt,
            _createSVG = function (type, container, attributes) {
                var element = document.createElementNS("http://www.w3.org/2000/svg", type),
                        reg = /([a-z])([A-Z])/g,
                        p;
                for (p in attributes) {
                    element.setAttributeNS(null, p.replace(reg, "$1-$2").toLowerCase(), attributes[p]);
                }
                container.appendChild(element);
                return element;
            },
            _pointToSegDist = function (x, y, x1, y1, x2, y2) {
                var dx = x2 - x1,
                        dy = y2 - y1,
                        t;
                if (dx || dy) {
                    t = ((x - x1) * dx + (y - y1) * dy) / (dx * dx + dy * dy);
                    if (t > 1) {
                        x1 = x2;
                        y1 = y2;
                    } else if (t > 0) {
                        x1 += dx * t;
                        y1 += dy * t;
                    }
                }
                dx = x - x1;
                dy = y - y1;
                return dx * dx + dy * dy;
            },
            _simplifyStep = function (points, first, last, tolerance, simplified) {
                var maxSqDist = tolerance,
                        firstX = points[first],
                        firstY = points[first + 1],
                        lastX = points[last],
                        lastY = points[last + 1],
                        index, i, d;
                for (i = first + 2; i < last; i += 2) {
                    d = _pointToSegDist(points[i], points[i + 1], firstX, firstY, lastX, lastY);
                    if (d > maxSqDist) {
                        index = i;
                        maxSqDist = d;
                    }
                }
                if (maxSqDist > tolerance) {
                    if (index - first > 2) {
                        _simplifyStep(points, first, index, tolerance, simplified);
                    }
                    simplified.push(points[index], points[index + 1]);
                    if (last - index > 2) {
                        _simplifyStep(points, index, last, tolerance, simplified);
                    }
                }
            },
            _simplifyPoints = function (points, tolerance) {
                var prevX = points[0],
                        prevY = points[1],
                        temp = [prevX, prevY],
                        l = points.length - 2,
                        i, x, y, dx, dy, result, last;
                tolerance = tolerance || 1;
                tolerance *= tolerance;
                for (i = 2; i < l; i += 2) {
                    x = points[i];
                    y = points[i + 1];
                    dx = prevX - x;
                    dy = prevY - y;
                    if (dx * dx + dy * dy > tolerance) {
                        temp.push(x, y);
                        prevX = x;
                        prevY = y;
                    }
                }
                temp.push(points[l], points[l + 1]);
                last = temp.length - 2;
                result = [temp[0], temp[1]];
                _simplifyStep(temp, 0, last, tolerance, result);
                result.push(temp[last], temp[last + 1]);
                return result;
            },
            _pointsToSegment = function (points, tolerance, cornerThreshold, curviness) {
                points = _simplifyPoints(points, tolerance);
                var l = points.length - 2,
                        x = +points[0],
                        y = +points[1],
                        nextX = +points[2],
                        nextY = +points[3],
                        segment = [x, y, x, y],
                        dx2 = nextX - x,
                        dy2 = nextY - y,
                        prevX, prevY, angle, slope, i, dx1, dx3, dy1, dy3, d1, d2, a, b, c;
                if (isNaN(cornerThreshold)) {
                    cornerThreshold = Math.PI / 10;
                }
                curviness = (curviness || curviness === 0) ? +curviness * 0.4 : 0.4;
                for (i = 2; i < l; i += 2) {
                    prevX = x;
                    prevY = y;
                    x = nextX;
                    y = nextY;
                    nextX = +points[i + 2];
                    nextY = +points[i + 3];
                    dx1 = dx2;
                    dy1 = dy2;
                    dx2 = nextX - x;
                    dy2 = nextY - y;
                    dx3 = nextX - prevX;
                    dy3 = nextY - prevY;
                    a = dx1 * dx1 + dy1 * dy1;
                    b = dx2 * dx2 + dy2 * dy2;
                    c = dx3 * dx3 + dy3 * dy3;
                    angle = Math.acos((a + b - c) / _sqrt(4 * a * b)); //angle between the 3 points
                    d2 = (angle / Math.PI) * curviness; //temporary precalculation for speed (reusing d2 variable)
                    d1 = _sqrt(a) * d2; //the tighter the angle, the shorter we make the handles in proportion.
                    d2 *= _sqrt(b);
                    if (angle > cornerThreshold) {
                        slope = _atan2(dy3, dx3);
                        segment.push(x - _cos(slope) * d1, y - _sin(slope) * d1, x, y, x + _cos(slope) * d2, y + _sin(slope) * d2);
                    } else {
                        slope = _atan2(dy1, dx1);
                        segment.push(x - _cos(slope) * d1, y - _sin(slope) * d1);
                        slope = _atan2(dy2, dx2);
                        segment.push(x, y, x + _cos(slope) * d2, y + _sin(slope) * d2);
                    }
                }
                segment.push(nextX, nextY, nextX, nextY);
                return segment;
            },
            self = this,
            path = tween.target[0] || tween.target,
            svg = path.ownerSVGElement,
            linesGroup = _createSVG("g", svg, {class: "morph-tracer morph-tracer-lines"}),
            anchorsGroup = _createSVG("g", svg, {class: "morph-tracer morph-tracer-anchors"}),
            origin = _createSVG("svg_object_2", svg, {r: vars.originSize, style: "fill:" + vars.originColor, class: "morph-tracer morph-tracer-origin"}),
            lines = [],
            anchors = [],
            _origCallback = function (name) {
                var callback = tween.vars[name],
                        scope = tween.vars[name + "Scope"] || tween,
                        params = tween.vars[name + "Params"] || [];
                if (callback) {
                    return function () {
                        callback.apply(scope, params);
                    }
                }
            },
            _origOnStart = _origCallback("onStart"),
            _origOnUpdate = _origCallback("onUpdate"),
            lengths = [],
            _initted,
            _init = function () {
                var time = tween.time(),
                        duration = tween.duration(),
                        l = vars.precision * 100,
                        inc = duration / l,
                        positions = [],
                        t, i, j, sl, segment, ai, rawPath, line, x, y, dx, dy, a, pos;
                for (t = 0; t < l; t++) {
                    tween.render((t * inc) || 0.00001, true);
                    rawPath = path._gsRawPath || MorphSVGPlugin.stringToRawPath(path.getAttribute("d"));
                    ai = 0; //anchor index
                    for (j = 0; j < rawPath.length; j++) {
                        segment = rawPath[j];
                        sl = segment.length;
                        for (i = 0; i < sl; i += 6) {
                            x = segment[i];
                            y = segment[i + 1];
                            pos = positions[ai];
                            if (!pos) {
                                positions[ai] = [x, y];
                                lengths[ai] = [0];
                                anchors[ai] = _createSVG("svg_object_2", anchorsGroup, {r: vars.anchorSize, style: "fill:" + vars.anchorColor});
                            } else {
                                dx = x - pos[pos.length - 2];
                                dy = y - pos[pos.length - 1];
                                pos.push(x, y);
                                lengths[ai].push(lengths[ai][lengths[ai].length - 1] + _sqrt(dx * dx + dy * dy));
                            }
                            ai++;
                        }
                    }
                }
                for (i = 0; i < positions.length; i++) {
                    line = _createSVG("path", linesGroup, {d: MorphSVGPlugin.rawPathToString([_pointsToSegment(positions[i], 0.5)]), class: "morph-tracer morph-tracer-line", fill: "none", style: "fill:none;opacity:" + vars.lineOpacity + ";stroke:" + vars.lineColor + ";stroke-width:" + vars.lineWidth});
                    lines.push(line);
                }
                for (j = 0; j < lengths.length; j++) {
                    a = lengths[j];
                    l = a[a.length - 1];
                    for (i = 0; i < a.length; i++) {
                        a[i] /= l;
                    }
                    lines[j].totalLength = lines[j].getTotalLength();
                    lines[j].style.strokeDasharray = lines[j].totalLength + "px, " + lines[j].totalLength + "px";
                    lines[j].style.strokeDashoffset = -lines[j].totalLength;
                }
                tween.render(time, true);
                _initted = true;
            };

    this.hide = function () {
        linesGroup.style.visibility = origin.style.visibility = anchorsGroup.style.visibility = "hidden";
    };

    this.show = function () {
        linesGroup.style.visibility = origin.style.visibility = anchorsGroup.style.visibility = "visible";
    };

    this.kill = function () {
        if (origin.parentNode) {
            origin.parentNode.removeChild(origin);
            anchorsGroup.parentNode.removeChild(anchorsGroup);
            linesGroup.parentNode.removeChild(linesGroup);
        }
        anchors.length = lines.length = 0;
    };

    tween.eventCallback("onStart", function () {
        if (!_initted) {
            _init();
        }
        if (_origOnStart) {
            _origOnStart();
        }
    }).eventCallback("onUpdate", function () {
        if (_initted && anchors.length) {
            var p = tween.progress() * (vars.precision * 100),
                    timeIndex = p | 0,
                    t = p - timeIndex,
                    rawPath = path._gsRawPath || MorphSVGPlugin.stringToRawPath(path.getAttribute("d")),
                    originPoint = rawPath.origin || {x: 0, y: 0},
                    ai = 0, //anchor index
                    rnd = 100,
                    i, j, segment, factor;
            //adjust the length of all the lines (strokeDashoffset)
            for (i = 0; i < lines.length; i++) {
                factor = lengths[i][timeIndex];
                factor += ((lengths[i][timeIndex + 1] - factor) || 0) * t;
                lines[i].style.strokeDashoffset = lines[i].totalLength * (1 - factor);
            }
            //position all the anchors
            for (j = 0; j < rawPath.length; j++) {
                segment = rawPath[j];
                for (i = 0; i < segment.length; i += 6) {
                    anchors[ai++].setAttribute("transform", "translate(" + (((segment[i] * rnd) | 0) / rnd) + "," + (((segment[i + 1] * rnd) | 0) / rnd) + ")");
                    //console.log(ai-1, anchors[ai-1].getAttribute("transform"));
                }
            }
            origin.setAttribute("transform", "translate(" + originPoint.x + ", " + originPoint.y + ")");
            if (path._gsMorphTracer !== self) {
                if (path._gsMorphTracer) {
                    path._gsMorphTracer.hide();
                }
                self.show();
                path._gsMorphTracer = self;
            }
        }
        if (_origOnUpdate) {
            _origOnUpdate();
        }
    });

    this.path = path;
}


MorphTracer.create = function (animation, vars) {
    var result = [],
            a = animation.getChildren ? animation.getChildren(true, true, false) : [animation],
            i;
    for (i = 0; i < a.length; i++) {
        if (a[i].vars.morphSVG) {
            result.push(new MorphTracer(a[i], vars));
        }
    }
    return result;
};

//tracer = MorphTracer.create(tl, {precision:0.5, lineOpacity:0.1});

start(true);