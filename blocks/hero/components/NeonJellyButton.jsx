import React, { useEffect, useMemo, useRef, useState } from "react";
import { motion, useMotionValue, useSpring, useTransform } from "framer-motion";

/**
 * NeonJellyMagnetButton - FIXED VERSION
 * - Spherical CTA with jelly + magnetic hover (FIXED magnetic radius)
 * - Rotating conic neon ring (cyan/magenta)
 * - Clean glass interior
 * - Comet orbiters with trailing echoes
 * - Pointer neon trail inside the sphere
 * - Click burst (radial rays) + ripples
 * - FIXED text wrapping and button sizing
 * - Accessible: keyboard + reduced-motion support
 */
export function InteractiveNeonButton({
  label = "Start a project",
  onClick,
  magneticRadius = 150, // Reduced for better control
  magneticStrength = 0.3, // Increased for more noticeable effect
  strobe = true,
  showOrbiters = true,
  showPointerTrail = true,
  showBurst = true,
}) {
  const btnRef = useRef(null);
  const containerRef = useRef(null);
  const [isPressed, setPressed] = useState(false);
  const [isHovered, setIsHovered] = useState(false);

  // Motion values for magnetic attraction
  const mx = useMotionValue(0);
  const my = useMotionValue(0);

  // Springs for jelly feel - adjusted for better response
  const sx = useSpring(mx, { stiffness: 400, damping: 30, mass: 0.2 });
  const sy = useSpring(my, { stiffness: 400, damping: 30, mass: 0.2 });

  // Slight parallax tilt
  const rotX = useTransform(sy, [-30, 30], [6, -6]);
  const rotY = useTransform(sx, [-30, 30], [-6, 6]);

  // Pressed squish
  const pressScaleX = isPressed ? 0.96 : 1;
  const pressScaleY = isPressed ? 0.92 : 1;

  const [ripples, setRipples] = useState([]);
  const [sparks, setSparks] = useState([]);
  const [bursts, setBursts] = useState([]);
  const ids = useRef({ ripple: 0, spark: 0, burst: 0 });
  const prevPt = useRef(null);

  // Responsive orbit radius
  const [orbitRadius, setOrbitRadius] = useState(64);
  useEffect(() => {
    if (!btnRef.current) return;
    const ro = new ResizeObserver(() => {
      if (!btnRef.current) return;
      const r = btnRef.current.getBoundingClientRect();
      setOrbitRadius(Math.max(42, r.width / 2 - 10));
    });
    ro.observe(btnRef.current);
    return () => ro.disconnect();
  }, []);

  // Handlers
  const isFinePointer = (pointerType) =>
    !pointerType || pointerType === "mouse" || pointerType === "pen";

  const handlePointerMove = (e) => {
    if (!isFinePointer(e.pointerType)) {
      return;
    }

    if (!btnRef.current) return;
    const rect = btnRef.current.getBoundingClientRect();
    const cx = rect.left + rect.width / 2;
    const cy = rect.top + rect.height / 2;
    const dx = e.clientX - cx;
    const dy = e.clientY - cy;
    const dist = Math.hypot(dx, dy);

    if (dist < magneticRadius) {
      setIsHovered(true);
      const pull = (1 - dist / magneticRadius) * (rect.width * magneticStrength);
      const nx = clamp(
        (dx / (dist || 1)) * pull,
        -rect.width * 0.3,
        rect.width * 0.3
      );
      const ny = clamp(
        (dy / (dist || 1)) * pull,
        -rect.height * 0.3,
        rect.height * 0.3
      );
      mx.set(nx);
      my.set(ny);

      if (showPointerTrail && dist < rect.width / 2) {
        const lx = e.clientX - rect.left;
        const ly = e.clientY - rect.top;
        const last = prevPt.current;
        const moved = last ? Math.hypot(lx - last.x, ly - last.y) : 999;
        if (moved > 10) {
          prevPt.current = { x: lx, y: ly };
          const c = Math.random() < 0.5 ? "c" : "m";
          const id = ++ids.current.spark;
          setSparks((s) =>
            (s.length > 12 ? s.slice(1) : s).concat({ id, x: lx, y: ly, c })
          );
          setTimeout(
            () => setSparks((s) => s.filter((sp) => sp.id !== id)),
            600
          );
        }
      }
    } else {
      setIsHovered(false);
      mx.set(0);
      my.set(0);
      prevPt.current = null;
    }
  };

  const handlePointerLeave = () => {
    setIsHovered(false);
    mx.set(0);
    my.set(0);
    setPressed(false);
    prevPt.current = null;
  };

  const handlePointerDown = (e) => {
    setPressed(true);
    if (isFinePointer(e.pointerType)) {
      spawnRipple(e);
    }
  };

  const handlePointerUp = (e) => {
    setPressed(false);
    if (isFinePointer(e.pointerType) && showBurst) {
      const id = ++ids.current.burst;
      setBursts((b) => [...b, { id }]);
      setTimeout(() => setBursts((b) => b.filter((x) => x.id !== id)), 600);
    }
    onClick?.();
  };

  const handleKeyDown = (e) => {
    if (e.key === "Enter" || e.key === " ") {
      e.preventDefault();
      setPressed(true);
    }
  };
  
  const handleKeyUp = (e) => {
    if (e.key === "Enter" || e.key === " ") {
      e.preventDefault();
      setPressed(false);
      onClick?.();
    }
  };

  function spawnRipple(e) {
    if (!btnRef.current) return;
    const rect = btnRef.current.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;
    const id = ++ids.current.ripple;
    setRipples((r) => [...r, { id, x, y }]);
    setTimeout(() => setRipples((r) => r.filter((it) => it.id !== id)), 720);
  }

  // Clean inner glass + subtle additive neon
  const innerGradient = useMemo(
    () => ({
      background:
        "radial-gradient(60% 60% at 50% 45%, #141a22 0%, #0c1016 55%, #090d12 100%)",
    }),
    []
  );
  
  const additiveNeon = useMemo(
    () => ({
      background:
        "radial-gradient(110% 110% at 30% 30%, rgba(0,255,255,0.22) 0%, rgba(0,0,0,0) 55%)," +
        "radial-gradient(110% 110% at 70% 70%, rgba(255,0,200,0.22) 0%, rgba(0,0,0,0) 55%)," +
        "radial-gradient(70% 70% at 50% 50%, rgba(255,255,255,0.08) 0%, rgba(0,0,0,0) 70%)",
    }),
    []
  );
  
  const supportsMask = useMemo(() => {
    if (typeof CSS === "undefined" || typeof CSS.supports !== "function") {
      return true;
    }
    const mask =
      "radial-gradient(circle at center, transparent 63%, rgba(0,0,0,.7) 72%, #000 78%)";
    return (
      CSS.supports("mask-image", mask) ||
      CSS.supports("-webkit-mask-image", mask)
    );
  }, []);

  return (
    <>
      <style>{`
        @keyframes neonPulse { 
          0%, 100% { 
            filter: drop-shadow(0 0 12px rgba(0,255,255,.5)) drop-shadow(0 0 16px rgba(255,0,200,.5)); 
          } 
          50% { 
            filter: drop-shadow(0 0 26px rgba(0,255,255,.85)) drop-shadow(0 0 32px rgba(255,0,200,.85)); 
          } 
        }
        @keyframes spin360 { to { transform: rotate(360deg); } }
        @keyframes orb { 
          from { transform: rotate(0deg) translateX(var(--orbit)) rotate(0deg); } 
          to { transform: rotate(360deg) translateX(var(--orbit)) rotate(-360deg); } 
        }
        @keyframes ripple { to { transform: translate(-50%, -50%) scale(4.3); opacity: 0; } }
        @keyframes spark { to { transform: translate(-50%, -50%) scale(1.8); opacity: 0; } }
        @keyframes burst { to { transform: scale(2.6); opacity: 0; } }
        
        @media (prefers-reduced-motion: reduce) {
          .rm * { animation: none !important; transition: none !important; }
        }
      `}</style>

      {/* SVG goo filter for comet tails */}
      <svg width="0" height="0" style={{ position: "absolute" }}>
        <defs>
          <filter id="goo" x="-50%" y="-50%" width="200%" height="200%">
            <feGaussianBlur in="SourceGraphic" stdDeviation="3" result="b" />
            <feColorMatrix
              in="b"
              mode="matrix"
              values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 18 -9"
              result="goo"
            />
            <feBlend in="SourceGraphic" in2="goo" />
          </filter>
        </defs>
      </svg>

      <motion.button
        ref={btnRef}
        role="button"
        aria-label={label}
        onPointerMove={handlePointerMove}
        onPointerLeave={handlePointerLeave}
        onPointerDown={handlePointerDown}
        onPointerUp={handlePointerUp}
        onKeyDown={handleKeyDown}
        onKeyUp={handleKeyUp}
        style={{
          position: "relative",
          userSelect: "none",
          isolation: "isolate",
          height: "clamp(7rem, 15vw, 9rem)",
          width: "clamp(7rem, 15vw, 9rem)",
          minHeight: "112px",
          minWidth: "112px",
          borderRadius: "9999px",
          outline: "none",
          border: "none",
          background: "transparent",
          cursor: "pointer",
          padding: 0,
          touchAction: "manipulation",
          WebkitTapHighlightColor: "transparent",
          boxShadow: isHovered
            ? "0 0 32px rgba(0,255,255,.5), 0 0 56px rgba(255,0,200,.5), inset 0 0 28px rgba(0,255,255,.3), inset 0 0 28px rgba(255,0,200,.3)"
            : "0 0 24px rgba(0,255,255,.35), 0 0 48px rgba(255,0,200,.35), inset 0 0 24px rgba(0,255,255,.25), inset 0 0 24px rgba(255,0,200,.25)",
          ...(strobe ? { animation: "neonPulse 1.6s ease-in-out infinite" } : {}),
          transformStyle: "preserve-3d",
          transition: "box-shadow 300ms ease-out, transform 200ms",
          x: sx,
          y: sy,
          scaleX: pressScaleX,
          scaleY: pressScaleY,
        }}
        className="rm"
      >
        {/* Magnetic transform & jelly squish */}
        <motion.div
          style={{
            position: "absolute",
            inset: 0,
            borderRadius: "9999px",
            willChange: "transform",
            rotateX: rotX,
            rotateY: rotY,
          }}
        >
          {/* Core sphere */}
          <div
            style={{
              position: "absolute",
              inset: 0,
              borderRadius: "9999px",
              overflow: "hidden",
              ...innerGradient,
            }}
          >
            {/* Additive neon paint */}
            <div
              style={{
                position: "absolute",
                inset: 0,
                mixBlendMode: "screen",
                ...additiveNeon,
              }}
            />

            {/* Rotating conic neon border (ring) */}
            {supportsMask ? (
              <div
                style={{
                  pointerEvents: "none",
                  position: "absolute",
                  inset: 0,
                  borderRadius: "9999px",
                  background:
                    "conic-gradient(rgba(0,255,255,.9), rgba(255,0,200,.9), rgba(0,255,255,.9))",
                  animation: "spin360 8s linear infinite",
                  filter: "blur(0.2px) saturate(1.2)",
                  WebkitMaskImage:
                    "radial-gradient(circle at center, transparent 63%, rgba(0,0,0,.7) 72%, #000 78%)",
                  WebkitMaskRepeat: "no-repeat",
                  WebkitMaskSize: "100% 100%",
                  maskImage:
                    "radial-gradient(circle at center, transparent 63%, rgba(0,0,0,.7) 72%, #000 78%)",
                  maskRepeat: "no-repeat",
                  maskSize: "100% 100%",
                  mixBlendMode: "screen",
                }}
              />
            ) : (
              <div
                style={{
                  pointerEvents: "none",
                  position: "absolute",
                  inset: 0,
                  borderRadius: "9999px",
                  background:
                    "radial-gradient(circle at center, rgba(0,0,0,0) 56%, rgba(0,255,255,0.42) 70%, rgba(255,0,200,0.38) 86%, rgba(0,0,0,0) 100%)",
                  filter: "blur(1.2px)",
                  opacity: 0.95,
                  mixBlendMode: "screen",
                }}
              />
            )}

            {/* Soft inner ring */}
            <div
              style={{
                position: "absolute",
                inset: "0.5rem",
                borderRadius: "9999px",
                boxShadow:
                  "inset 0 0 26px rgba(0,255,255,.22), inset 0 0 32px rgba(255,0,200,.22), inset 0 0 6px rgba(255,255,255,.06)",
              }}
            />

            {/* Specular highlight */}
            <div
              style={{
                position: "absolute",
                pointerEvents: "none",
                left: "18%",
                top: "22%",
                width: "28%",
                height: "28%",
                borderRadius: "9999px",
                background:
                  "radial-gradient(circle at 30% 30%, rgba(255,255,255,.22), rgba(255,255,255,.06) 60%, transparent 70%)",
                mixBlendMode: "screen",
                filter: "blur(.2px)",
              }}
            />

            {/* Orbiting comets (with gooey filter + echoes) */}
            {showOrbiters && (
              <div
                style={{
                  position: "absolute",
                  inset: 0,
                  display: "grid",
                  placeItems: "center",
                  pointerEvents: "none",
                  filter: "url(#goo)",
                }}
              >
                <div
                  style={{
                    position: "relative",
                    height: 0,
                    width: 0,
                    ["--orbit"]: `${orbitRadius}px`,
                  }}
                >
                  {[0, 1, 2].map((i) => (
                    <span
                      key={`c${i}`}
                      style={{
                        position: "absolute",
                        height: "0.5rem",
                        width: "0.5rem",
                        borderRadius: "9999px",
                        top: 0,
                        left: 0,
                        transformOrigin: "0 0",
                        animation: `orb ${5.8 + i}s linear infinite`,
                        background: i % 2 ? "#00ffff" : "#ff00c8",
                        boxShadow: "0 0 12px currentColor, 0 0 18px currentColor",
                      }}
                    />
                  ))}
                  {/* trailing echoes */}
                  {[0, 1, 2, 3, 4].map((i) => (
                    <span
                      key={`e${i}`}
                      style={{
                        position: "absolute",
                        height: "6px",
                        width: "6px",
                        borderRadius: "9999px",
                        opacity: 0.25,
                        top: 0,
                        left: 0,
                        transformOrigin: "0 0",
                        animation: `orb ${6.2 + i * 0.2}s linear infinite`,
                        animationDelay: `${-i * 0.25}s`,
                        background: i % 2 ? "#00ffff" : "#ff00c8",
                        filter: "blur(2px)",
                      }}
                    />
                  ))}
                </div>
              </div>
            )}

            {/* Pointer neon trail */}
            {showPointerTrail && (
              <div style={{ position: "absolute", inset: 0, pointerEvents: "none" }}>
                {sparks.map((s) => (
                  <span
                    key={s.id}
                    style={{
                      position: "absolute",
                      height: "0.5rem",
                      width: "0.5rem",
                      borderRadius: "9999px",
                      left: s.x,
                      top: s.y,
                      transform: "translate(-50%, -50%) scale(.6)",
                      background: s.c === "c" ? "#00ffff" : "#ff00c8",
                      boxShadow:
                        s.c === "c"
                          ? "0 0 12px rgba(0,255,255,.9)"
                          : "0 0 12px rgba(255,0,200,.9)",
                      animation: "spark 600ms ease-out forwards",
                      mixBlendMode: "screen",
                    }}
                  />
                ))}
              </div>
            )}

            {/* Click ripples */}
            <div style={{ position: "absolute", inset: 0 }}>
              {ripples.map((r) => (
                <span
                  key={r.id}
                  style={{
                    pointerEvents: "none",
                    position: "absolute",
                    aspectRatio: "1",
                    borderRadius: "9999px",
                    border: "1px solid rgba(255,255,255,.5)",
                    left: r.x,
                    top: r.y,
                    transform: "translate(-50%, -50%) scale(0.25)",
                    boxShadow:
                      "0 0 12px rgba(0,255,255,.5), 0 0 12px rgba(255,0,200,.5)",
                    animation: "ripple 720ms ease-out forwards",
                    mixBlendMode: "screen",
                  }}
                />
              ))}
            </div>

            {/* Burst rays on click */}
            {showBurst && (
              <div style={{ position: "absolute", inset: 0, pointerEvents: "none" }}>
                {bursts.map((b) => (
                  <div
                    key={b.id}
                    style={{
                      position: "absolute",
                      inset: 0,
                      borderRadius: "9999px",
                      background:
                        "repeating-conic-gradient(from 0deg, rgba(0,255,255,.35) 0deg 6deg, rgba(255,0,200,.35) 6deg 12deg)",
                      mixBlendMode: "screen",
                      transform: "scale(.7)",
                      animation: "burst 600ms ease-out forwards",
                      filter: "blur(0.6px)",
                    }}
                  />
                ))}
              </div>
            )}

            {/* FIXED Label Container */}
            <div
              style={{
                position: "absolute",
                inset: 0,
                display: "flex",
                alignItems: "center",
                justifyContent: "center",
                padding: "0 clamp(12px, 3vw, 20px)",
              }}
            >
              <span
                style={{
                  pointerEvents: "none",
                  textAlign: "center",
                  fontWeight: 600,
                  letterSpacing: "0.02em",
                  fontSize: "clamp(10px, 2vw, 13px)",
                  lineHeight: 1.2,
                  color: "#eaf9ff",
                  textShadow:
                    "0 0 6px rgba(0,255,255,.55), 0 0 10px rgba(255,0,200,.55)",
                  userSelect: "none",
                  whiteSpace: "normal",
                  wordBreak: "break-word",
                  hyphens: "auto",
                  maxWidth: "100%",
                  display: "block",
                  textTransform: "uppercase",
                }}
              >
                {label}
              </span>
            </div>
          </div>
        </motion.div>
      </motion.button>
    </>
  );
}

function clamp(n, a, b) {
  return Math.max(a, Math.min(b, n));
}
