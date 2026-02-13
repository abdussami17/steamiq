<style>

.tournament-pinned-matches-section {
            margin-bottom: 2rem;
            position: sticky;
            top: 1rem;
            z-index: 100;
            background: #0f0f0f;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
            border: 1px solid #2a2a2a;
            display: none;  
        }

        .tournament-pinned-matches-section.tournament-active-pinned-section {
            display: block;
            animation: tournamentSlideDown 0.4s ease-out;
        }

        @keyframes tournamentSlideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .tournament-pinned-section-header {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .tournament-pinned-indicator-icon {
            width: 20px;
            height: 20px;
            background: #ffffff;
            border-radius: 50%;
            display: inline-block;
        }

        .tournament-all-matches-section {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 2rem;
}


        .tournament-match-card-container {
            background: linear-gradient(145deg, #1a1a1a 0%, #0f0f0f 100%);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
            border: 1px solid #2a2a2a;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .tournament-match-card-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #ffffff 0%, #666666 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .tournament-match-card-container:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.6);
            border-color: #3a3a3a;
        }

        .tournament-match-card-container:hover::before {
            opacity: 1;
        }

        .tournament-match-card-container.tournament-pinned-match-state {
            border-color: #ffffff;
            box-shadow: 0 10px 40px rgba(255, 255, 255, 0.1);
        }

        .tournament-match-card-container.tournament-pinned-match-state::before {
            opacity: 1;
        }

        .tournament-match-card-container.tournament-completed-match-state {
            opacity: 0.7;
        }

        .tournament-match-header-section {
            margin-bottom: 2rem;
            text-align: center;
        }

        .tournament-match-name-primary {
            font-size: 1.5rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 0.75rem;
            letter-spacing: -0.5px;
        }

        .tournament-match-type-label {
            display: inline-block;
            padding: 0.4rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            color: #cccccc;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .tournament-teams-versus-section {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            gap: 1.5rem;
            margin: 2rem 0;
        }

        .tournament-team-display-block {
            text-align: center;
            padding: 2rem 1.5rem;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 16px;
            border: 2px solid transparent;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            min-height: 140px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .tournament-team-display-block:hover {
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(255, 255, 255, 0.2);
            transform: scale(1.05);
        }

        .tournament-team-display-block.tournament-winning-team-state {
            background: linear-gradient(135deg, #ffffff 0%, #d0d0d0 100%);
            border-color: #ffffff;
            animation: tournamentWinnerPulse 0.6s ease-out;
        }

        .tournament-team-display-block.tournament-winning-team-state .tournament-team-name-text {
            color: #000000;
            font-weight: 800;
        }

        .tournament-team-display-block.tournament-losing-team-state {
            opacity: 0.4;
            cursor: not-allowed;
        }

        @keyframes tournamentWinnerPulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.08);
            }
        }

        .tournament-team-name-text {
            font-size: 1.25rem;
            font-weight: 600;
            color: #ffffff;
            transition: all 0.3s ease;
            line-height: 1.4;
        }

        .tournament-versus-image-container {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .tournament-versus-badge-graphic {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #2a2a2a 0%, #1a1a1a 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid #3a3a3a;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.5);
            position: relative;
            transition: all 0.3s ease;
        }

        .tournament-versus-badge-graphic::before {
            content: '';
            position: absolute;
            inset: -6px;
            background: linear-gradient(135deg, #ffffff 0%, #666666 100%);
            border-radius: 50%;
            z-index: -1;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .tournament-match-card-container:hover .tournament-versus-badge-graphic::before {
            opacity: 0.3;
        }

        .tournament-versus-badge-graphic svg {
            width: 45px;
            height: 45px;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
        }

        .tournament-match-actions-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-top: 2rem;
        }

        .tournament-pin-match-button {
            padding: 1rem 1.5rem;
            background: rgba(255, 255, 255, 0.08);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            color: #ffffff;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .tournament-pin-match-button:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.4);
            transform: translateY(-2px);
        }

        .tournament-pin-match-button.tournament-active-pin-state {
            background: #ffffff;
            color: #000000;
            border-color: #ffffff;
        }

        .tournament-select-winner-button {
            padding: 1rem 1.5rem;
            background: linear-gradient(135deg, #2a2a2a 0%, #1a1a1a 100%);
            border: 2px solid #3a3a3a;
            border-radius: 12px;
            color: #ffffff;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .tournament-select-winner-button:hover:not(:disabled) {
            background: linear-gradient(135deg, #3a3a3a 0%, #2a2a2a 100%);
            border-color: #4a4a4a;
            transform: translateY(-2px);
        }

        .tournament-select-winner-button:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .tournament-winner-badge-indicator {
            position: absolute;
            top: -12px;
            right: -12px;
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #ffffff 0%, #e0e0e0 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            color: #000000;
            font-size: 1.4rem;
            opacity: 0;
            transform: scale(0);
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(255, 255, 255, 0.3);
            border: 3px solid #000000;
        }

        .tournament-team-display-block.tournament-winning-team-state .tournament-winner-badge-indicator {
            opacity: 1;
            transform: scale(1);
            animation: tournamentBadgeBounce 0.5s ease-out 0.2s;
        }

        @keyframes tournamentBadgeBounce {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.2);
            }
        }

        @media (max-width: 992px) {
            .tournament-all-matches-section {
                grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .tournament-dashboard-page-title {
                font-size: 2rem;
            }

            .tournament-all-matches-section {
                grid-template-columns: 1fr;
            }

            .tournament-teams-versus-section {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .tournament-versus-image-container {
                order: 2;
                margin: 1rem 0;
            }

            .tournament-team-display-block:first-child {
                order: 1;
            }

            .tournament-team-display-block:last-child {
                order: 3;
            }

            .tournament-versus-badge-graphic {
                width: 70px;
                height: 70px;
            }

            .tournament-versus-badge-graphic svg {
                width: 38px;
                height: 38px;
            }

            .tournament-match-actions-section {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .tournament-match-card-container {
                padding: 1.5rem;
            }

            .tournament-dashboard-page-title {
                font-size: 1.5rem;
            }

            .tournament-match-name-primary {
                font-size: 1.2rem;
            }

            .tournament-team-name-text {
                font-size: 1.1rem;
            }

            .tournament-team-display-block {
                padding: 1.5rem 1rem;
                min-height: 120px;
            }
        }
</style>